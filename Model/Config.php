<?php
namespace MyCompany\CustomerBlocklist\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Store\Model\ScopeInterface;

class Config
{
    private const XML_PATH_ENABLED = 'customerblocklist/general/enabled';
    private const XML_PATH_MESSAGE = 'customerblocklist/general/checkout_message';
    private const XML_PATH_BLACKLIST = 'customerblocklist/rules/blacklist';

    private ScopeConfigInterface $scopeConfig;
    private Json $json;

    public function __construct(ScopeConfigInterface $scopeConfig, Json $json)
    {
        $this->scopeConfig = $scopeConfig;
        $this->json = $json;
    }

    public function isEnabled(?int $storeId = null): bool
    {
        return $this->scopeConfig->isSetFlag(self::XML_PATH_ENABLED, ScopeInterface::SCOPE_STORE, $storeId);
    }

    public function getCheckoutMessage(?int $storeId = null): string
    {
        $value = (string)$this->scopeConfig->getValue(self::XML_PATH_MESSAGE, ScopeInterface::SCOPE_STORE, $storeId);
        return $value !== '' ? $value : 'We cannot process your order. Please contact support.';
    }

    public function getBlacklistRules(?int $storeId = null): array
    {
        return $this->getRules(self::XML_PATH_BLACKLIST, $storeId);
    }

    private function getRules(string $path, ?int $storeId = null): array
    {
        $raw = $this->scopeConfig->getValue($path, ScopeInterface::SCOPE_STORE, $storeId);
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

            $item = [
                'active' => !isset($row['active']) || (string)$row['active'] === '1' ? '1' : '0',
                'email' => trim((string)($row['email'] ?? '')),
                'telephone' => trim((string)($row['telephone'] ?? '')),
                'firstname' => trim((string)($row['firstname'] ?? '')),
                'lastname' => trim((string)($row['lastname'] ?? '')),
                'note' => trim((string)($row['note'] ?? '')),
            ];

            if ($item['email'] === '' && $item['telephone'] === '' && ($item['firstname'] === '' || $item['lastname'] === '')) {
                continue;
            }

            $result[] = $item;
        }

        return $result;
    }
}
