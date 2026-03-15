<?php
namespace MyCompany\CustomerBlocklist\Model;

use Magento\Framework\HTTP\PhpEnvironment\RemoteAddress;
use MyCompany\CustomerBlocklist\Model\AttemptLogFactory;
use MyCompany\CustomerBlocklist\Model\ResourceModel\AttemptLog as AttemptLogResource;

class AttemptLogger
{
    private AttemptLogFactory $attemptLogFactory;
    private AttemptLogResource $attemptLogResource;
    private RemoteAddress $remoteAddress;

    public function __construct(
        AttemptLogFactory $attemptLogFactory,
        AttemptLogResource $attemptLogResource,
        RemoteAddress $remoteAddress
    ) {
        $this->attemptLogFactory = $attemptLogFactory;
        $this->attemptLogResource = $attemptLogResource;
        $this->remoteAddress = $remoteAddress;
    }

    public function log(int $quoteId, int $storeId, CheckoutContext $context, array $match): void
    {
        $log = $this->attemptLogFactory->create();
        $log->setData([
            'quote_id' => $quoteId,
            'store_id' => $storeId,
            'customer_email' => $context->getEmail(),
            'telephone' => $context->getTelephone(),
            'firstname' => $context->getFirstname(),
            'lastname' => $context->getLastname(),
            'matched_list' => (string)($match['list'] ?? ''),
            'matched_field' => (string)($match['field'] ?? ''),
            'matched_value' => (string)($match['value'] ?? ''),
            'remote_ip' => (string)$this->remoteAddress->getRemoteAddress(),
        ]);
        $this->attemptLogResource->save($log);
    }
}
