<?php
namespace MyCompany\CustomerBlocklist\Model;

class RuleMatcher
{
    private Normalizer $normalizer;

    public function __construct(Normalizer $normalizer)
    {
        $this->normalizer = $normalizer;
    }

    public function getMatch(array $rules, CheckoutContext $context, string $listType): ?array
    {
        $email = $this->normalizer->normalizeEmail($context->getEmail());
        $telephone = $this->normalizer->normalizeTelephone($context->getTelephone());
        $firstname = $this->normalizer->normalizeName($context->getFirstname());
        $lastname = $this->normalizer->normalizeName($context->getLastname());

        foreach ($rules as $rule) {
            if (!is_array($rule)) {
                continue;
            }

            if (isset($rule['active']) && (string)$rule['active'] !== '1') {
                continue;
            }

            $ruleEmail = $this->normalizer->normalizeEmail((string)($rule['email'] ?? ''));
            if ($ruleEmail !== '' && $email !== '' && $ruleEmail === $email) {
                return ['list' => $listType, 'field' => 'email', 'value' => $rule['email'] ?? '', 'note' => $rule['note'] ?? ''];
            }

            $ruleTelephone = $this->normalizer->normalizeTelephone((string)($rule['telephone'] ?? ''));
            if ($ruleTelephone !== '' && $telephone !== '' && $ruleTelephone === $telephone) {
                return ['list' => $listType, 'field' => 'telephone', 'value' => $rule['telephone'] ?? '', 'note' => $rule['note'] ?? ''];
            }

            $ruleFirstname = $this->normalizer->normalizeName((string)($rule['firstname'] ?? ''));
            $ruleLastname = $this->normalizer->normalizeName((string)($rule['lastname'] ?? ''));
            if ($ruleFirstname !== '' && $ruleLastname !== '' && $firstname !== '' && $lastname !== '' && $ruleFirstname === $firstname && $ruleLastname === $lastname) {
                return ['list' => $listType, 'field' => 'name', 'value' => trim(($rule['firstname'] ?? '') . ' ' . ($rule['lastname'] ?? '')), 'note' => $rule['note'] ?? ''];
            }
        }

        return null;
    }
}
