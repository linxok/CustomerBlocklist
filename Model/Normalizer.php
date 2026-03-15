<?php
namespace MyCompany\CustomerBlocklist\Model;

class Normalizer
{
    public function normalizeEmail(?string $value): string
    {
        return mb_strtolower(trim((string)$value));
    }

    public function normalizeTelephone(?string $value): string
    {
        return preg_replace('/\D+/', '', (string)$value) ?: '';
    }

    public function getTelephoneVariants(?string $value): array
    {
        $normalized = $this->normalizeTelephone($value);
        if ($normalized === '') {
            return [];
        }

        $variants = [$normalized];

        if (strlen($normalized) > 10 && str_starts_with($normalized, '380')) {
            $variants[] = '0' . substr($normalized, 3);
        }

        if (strlen($normalized) === 10 && str_starts_with($normalized, '0')) {
            $variants[] = '38' . $normalized;
        }

        return array_values(array_unique(array_filter($variants)));
    }

    public function normalizeName(?string $value): string
    {
        $value = trim((string)$value);
        $value = preg_replace('/\s+/u', ' ', $value) ?: '';
        return mb_strtolower($value);
    }
}
