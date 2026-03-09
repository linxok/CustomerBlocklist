<?php

namespace MyCompany\CustomerBlocklist\Model;

class RuleTransferProcessor
{
    private Normalizer $normalizer;

    public function __construct(Normalizer $normalizer)
    {
        $this->normalizer = $normalizer;
    }

    public function process(array $blacklistRows, array $whitelistRows): array
    {
        $blacklistRows = $this->normalizeRows($blacklistRows);
        $whitelistRows = $this->normalizeRows($whitelistRows);

        $result = $this->applyTransfers($blacklistRows, $whitelistRows, 'whitelist');
        $blacklistRows = $result['source'];
        $whitelistRows = $result['target'];

        $result = $this->applyTransfers($whitelistRows, $blacklistRows, 'blacklist');
        $whitelistRows = $result['source'];
        $blacklistRows = $result['target'];

        return [
            'blacklist' => array_values($this->stripTransferAction($blacklistRows)),
            'whitelist' => array_values($this->stripTransferAction($whitelistRows)),
        ];
    }

    private function applyTransfers(array $sourceRows, array $targetRows, string $targetList): array
    {
        foreach ($sourceRows as $index => $row) {
            if (($row['transfer_action'] ?? '') !== $targetList) {
                continue;
            }

            unset($sourceRows[$index]);

            if (!$this->containsRule($targetRows, $row)) {
                $row['transfer_action'] = '';
                $targetRows[] = $row;
            }
        }

        return [
            'source' => array_values($sourceRows),
            'target' => array_values($targetRows),
        ];
    }

    private function normalizeRows(array $rows): array
    {
        unset($rows['__empty']);

        $result = [];
        foreach ($rows as $row) {
            if (!is_array($row)) {
                continue;
            }

            $normalized = [
                'email' => trim((string)($row['email'] ?? '')),
                'telephone' => trim((string)($row['telephone'] ?? '')),
                'firstname' => trim((string)($row['firstname'] ?? '')),
                'lastname' => trim((string)($row['lastname'] ?? '')),
                'note' => trim((string)($row['note'] ?? '')),
                'transfer_action' => trim((string)($row['transfer_action'] ?? '')),
            ];

            if (
                $normalized['email'] === ''
                && $normalized['telephone'] === ''
                && $normalized['firstname'] === ''
                && $normalized['lastname'] === ''
                && $normalized['note'] === ''
            ) {
                continue;
            }

            $result[] = $normalized;
        }

        return $result;
    }

    private function stripTransferAction(array $rows): array
    {
        foreach ($rows as &$row) {
            unset($row['transfer_action']);
        }

        return $rows;
    }

    private function containsRule(array $rules, array $rule): bool
    {
        foreach ($rules as $existingRule) {
            if ($this->isSameRule($existingRule, $rule)) {
                return true;
            }
        }

        return false;
    }

    private function isSameRule(array $existingRule, array $rule): bool
    {
        $existingEmail = $this->normalizer->normalizeEmail((string)($existingRule['email'] ?? ''));
        $ruleEmail = $this->normalizer->normalizeEmail((string)($rule['email'] ?? ''));

        if ($existingEmail !== '' && $ruleEmail !== '' && $existingEmail === $ruleEmail) {
            return true;
        }

        $existingTelephone = $this->normalizer->normalizeTelephone((string)($existingRule['telephone'] ?? ''));
        $ruleTelephone = $this->normalizer->normalizeTelephone((string)($rule['telephone'] ?? ''));

        if ($existingTelephone !== '' && $ruleTelephone !== '' && $existingTelephone === $ruleTelephone) {
            return true;
        }

        $existingFirstname = $this->normalizer->normalizeName((string)($existingRule['firstname'] ?? ''));
        $existingLastname = $this->normalizer->normalizeName((string)($existingRule['lastname'] ?? ''));
        $ruleFirstname = $this->normalizer->normalizeName((string)($rule['firstname'] ?? ''));
        $ruleLastname = $this->normalizer->normalizeName((string)($rule['lastname'] ?? ''));

        return $existingFirstname !== ''
            && $existingLastname !== ''
            && $ruleFirstname !== ''
            && $ruleLastname !== ''
            && $existingFirstname === $ruleFirstname
            && $existingLastname === $ruleLastname;
    }
}
