<?php
namespace MyCompany\CustomerBlocklist\Model;

use Magento\Framework\App\Config\ReinitableConfigInterface;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Sales\Api\Data\OrderInterface;

class AddOrderToBlacklist
{
    private const XML_PATH_BLACKLIST = 'customerblocklist/rules/blacklist';

    private Config $config;
    private WriterInterface $configWriter;
    private Json $json;
    private Normalizer $normalizer;
    private ReinitableConfigInterface $reinitableConfig;

    public function __construct(
        Config $config,
        WriterInterface $configWriter,
        Json $json,
        Normalizer $normalizer,
        ReinitableConfigInterface $reinitableConfig
    ) {
        $this->config = $config;
        $this->configWriter = $configWriter;
        $this->json = $json;
        $this->normalizer = $normalizer;
        $this->reinitableConfig = $reinitableConfig;
    }

    public function execute(OrderInterface $order): bool
    {
        $billingAddress = $order->getBillingAddress();
        $rule = [
            'email' => trim((string)$order->getCustomerEmail()),
            'telephone' => trim((string)($billingAddress ? $billingAddress->getTelephone() : '')),
            'firstname' => trim((string)($billingAddress ? $billingAddress->getFirstname() : '')),
            'lastname' => trim((string)($billingAddress ? $billingAddress->getLastname() : '')),
            'note' => (string)__('Added from order #%1', $order->getIncrementId()),
        ];

        if (
            $rule['email'] === ''
            && $rule['telephone'] === ''
            && ($rule['firstname'] === '' || $rule['lastname'] === '')
        ) {
            throw new LocalizedException(__('Order does not contain enough customer data to add to the blacklist.'));
        }

        $this->reinitableConfig->reinit();
        $rules = $this->config->getBlacklistRules();

        foreach ($rules as $existingRule) {
            if ($this->isSameRule($existingRule, $rule)) {
                return false;
            }
        }

        $rules[] = $rule;
        $this->configWriter->save(
            self::XML_PATH_BLACKLIST,
            $this->json->serialize(array_values($rules)),
            'default',
            0
        );

        return true;
    }

    private function isSameRule(array $existingRule, array $rule): bool
    {
        return $this->normalizer->normalizeEmail((string)($existingRule['email'] ?? ''))
                === $this->normalizer->normalizeEmail($rule['email'])
            && $this->normalizer->normalizeTelephone((string)($existingRule['telephone'] ?? ''))
                === $this->normalizer->normalizeTelephone($rule['telephone'])
            && $this->normalizer->normalizeName((string)($existingRule['firstname'] ?? ''))
                === $this->normalizer->normalizeName($rule['firstname'])
            && $this->normalizer->normalizeName((string)($existingRule['lastname'] ?? ''))
                === $this->normalizer->normalizeName($rule['lastname']);
    }
}
