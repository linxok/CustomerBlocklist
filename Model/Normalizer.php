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

    public function normalizeName(?string $value): string
    {
        $value = trim((string)$value);
        $value = preg_replace('/\s+/u', ' ', $value) ?: '';
        return mb_strtolower($value);
    }
}
