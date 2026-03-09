<?php

declare(strict_types=1);

namespace MyCompany\CustomerBlocklist\Model\Config\Backend;

use Magento\Config\Model\Config\Backend\Serialized\ArraySerialized;

class BlacklistRules extends ArraySerialized
{
    public function beforeSave()
    {
        $value = $this->getValue();

        if (!is_array($value)) {
            $this->setValue([]);
            return parent::beforeSave();
        }

        unset($value['__empty']);

        $filtered = [];
        foreach ($value as $rowId => $row) {
            if (!is_array($row)) {
                continue;
            }

            $normalizedRow = [
                'active' => isset($row['active']) && (string)$row['active'] === '0' ? '0' : '1',
                'email' => trim((string)($row['email'] ?? '')),
                'telephone' => trim((string)($row['telephone'] ?? '')),
                'firstname' => trim((string)($row['firstname'] ?? '')),
                'lastname' => trim((string)($row['lastname'] ?? '')),
                'note' => trim((string)($row['note'] ?? '')),
            ];

            if (
                $normalizedRow['email'] === ''
                && $normalizedRow['telephone'] === ''
                && $normalizedRow['firstname'] === ''
                && $normalizedRow['lastname'] === ''
            ) {
                continue;
            }

            $filtered[$rowId] = $normalizedRow;
        }

        $this->setValue($filtered);

        return parent::beforeSave();
    }
}
