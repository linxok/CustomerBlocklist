<?php
namespace MyCompany\CustomerBlocklist\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Serialize\Serializer\Json;

class MoveRuleBetweenLists
{
    private const XML_PATH_BLACKLIST = 'customerblocklist/rules/blacklist';
    private const XML_PATH_WHITELIST = 'customerblocklist/rules/whitelist';

    private ScopeConfigInterface $scopeConfig;
    private WriterInterface $configWriter;
    private Json $json;
    private Normalizer $normalizer;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        WriterInterface $configWriter,
        Json $json,
        Normalizer $normalizer
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->configWriter = $configWriter;
        $this->json = $json;
        $this->normalizer = $normalizer;
    }

    public function execute(string $sourceList, string $targetList, array $rule): bool
    {
        if (!$this->isSupportedList($sourceList) || !$this->isSupportedList($targetList) || $sourceList === $targetList) {
            throw new LocalizedException(__('Invalid source or target list.'));
        }

        $rule = $this->normalizeRule($rule);
        if ($rule['email'] === '' && $rule['telephone'] === '' && ($rule['firstname'] === '' || $rule['lastname'] === '')) {
            throw new LocalizedException(__('Rule does not contain enough data to move.'));
        }

        $sourceRules = $this->getRules($sourceList);
        $targetRules = $this->getRules($targetList);
        $sourceChanged = false;

        foreach ($sourceRules as $index => $existingRule) {
            if ($this->isSameRule($existingRule, $rule)) {
                unset($sourceRules[$index]);
                $sourceChanged = true;
                break;
            }
        }

        if (!$sourceChanged) {
            throw new LocalizedException(__('The selected rule was not found in the source list.'));
        }

        $targetExists = false;
        foreach ($targetRules as $existingRule) {
            if ($this->isSameRule($existingRule, $rule)) {
                $targetExists = true;
                break;
            }
        }

        if (!$targetExists) {
            $targetRules[] = $rule;
        }

        $this->saveRules($sourceList, array_values($sourceRules));
        $this->saveRules($targetList, array_values($targetRules));

        return !$targetExists;
    }

    private function getRules(string $list): array
    {
        $raw = $this->scopeConfig->getValue($this->getPathByList($list), ScopeConfigInterface::SCOPE_TYPE_DEFAULT);
        if (!$raw) {
            return [];
        }

        try {
            $rows = is_array($raw) ? $raw : $this->json->unserialize((string)$raw);
        } catch (\InvalidArgumentException $e) {
            return [];
        }

        if (!is_array($rows)) {
            return [];
        }

        $result = [];
        foreach ($rows as $row) {
            if (!is_array($row)) {
                continue;
            }
            $result[] = $this->normalizeRule($row);
        }

        return $result;
    }

    private function saveRules(string $list, array $rules): void
    {
        $this->configWriter->save(
            $this->getPathByList($list),
            $this->json->serialize($rules),
            ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
            0
        );
    }

    private function getPathByList(string $list): string
    {
        return $list === 'blacklist' ? self::XML_PATH_BLACKLIST : self::XML_PATH_WHITELIST;
    }

    private function isSupportedList(string $list): bool
    {
        return in_array($list, ['blacklist', 'whitelist'], true);
    }

    private function normalizeRule(array $rule): array
    {
        return [
            'email' => trim((string)($rule['email'] ?? '')),
            'telephone' => trim((string)($rule['telephone'] ?? '')),
            'firstname' => trim((string)($rule['firstname'] ?? '')),
            'lastname' => trim((string)($rule['lastname'] ?? '')),
            'note' => trim((string)($rule['note'] ?? '')),
        ];
    }

    private function isSameRule(array $existingRule, array $rule): bool
    {
        return $this->normalizer->normalizeEmail((string)($existingRule['email'] ?? ''))
                === $this->normalizer->normalizeEmail((string)($rule['email'] ?? ''))
            && $this->normalizer->normalizeTelephone((string)($existingRule['telephone'] ?? ''))
                === $this->normalizer->normalizeTelephone((string)($rule['telephone'] ?? ''))
            && $this->normalizer->normalizeName((string)($existingRule['firstname'] ?? ''))
                === $this->normalizer->normalizeName((string)($rule['firstname'] ?? ''))
            && $this->normalizer->normalizeName((string)($existingRule['lastname'] ?? ''))
                === $this->normalizer->normalizeName((string)($rule['lastname'] ?? ''));
    }
}
