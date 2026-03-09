<?php
namespace MyCompany\CustomerBlocklist\Model;

use Magento\Quote\Api\Data\AddressInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Model\QuoteIdMaskFactory;

class Validator
{
    private Config $config;
    private RuleMatcher $ruleMatcher;
    private AttemptLogger $attemptLogger;
    private CartRepositoryInterface $cartRepository;
    private QuoteIdMaskFactory $quoteIdMaskFactory;

    public function __construct(
        Config $config,
        RuleMatcher $ruleMatcher,
        AttemptLogger $attemptLogger,
        CartRepositoryInterface $cartRepository,
        QuoteIdMaskFactory $quoteIdMaskFactory
    ) {
        $this->config = $config;
        $this->ruleMatcher = $ruleMatcher;
        $this->attemptLogger = $attemptLogger;
        $this->cartRepository = $cartRepository;
        $this->quoteIdMaskFactory = $quoteIdMaskFactory;
    }

    public function validateByCartId($cartId, ?AddressInterface $billingAddress = null, ?string $guestEmail = null): void
    {
        $quote = $this->loadQuote($cartId);
        $storeId = (int)$quote->getStoreId();
        if (!$this->config->isEnabled($storeId)) {
            return;
        }

        $quoteBillingAddress = $quote->getBillingAddress();
        $context = new CheckoutContext(
            (string)($guestEmail ?: $quote->getCustomerEmail() ?: ($billingAddress ? $billingAddress->getEmail() : '') ?: ($quoteBillingAddress ? $quoteBillingAddress->getEmail() : '')),
            (string)(($billingAddress ? $billingAddress->getTelephone() : '') ?: ($quoteBillingAddress ? $quoteBillingAddress->getTelephone() : '')),
            (string)(($billingAddress ? $billingAddress->getFirstname() : '') ?: ($quoteBillingAddress ? $quoteBillingAddress->getFirstname() : '')),
            (string)(($billingAddress ? $billingAddress->getLastname() : '') ?: ($quoteBillingAddress ? $quoteBillingAddress->getLastname() : ''))
        );

        $whitelistMatch = $this->ruleMatcher->getMatch($this->config->getWhitelistRules($storeId), $context, 'whitelist');
        if ($whitelistMatch) {
            return;
        }

        $blacklistMatch = $this->ruleMatcher->getMatch($this->config->getBlacklistRules($storeId), $context, 'blacklist');
        if (!$blacklistMatch) {
            return;
        }

        $this->attemptLogger->log((int)$quote->getId(), $storeId, $context, $blacklistMatch);
        throw new LocalizedException(__($this->config->getCheckoutMessage($storeId)));
    }

    private function loadQuote($cartId)
    {
        if (is_numeric($cartId)) {
            return $this->cartRepository->getActive((int)$cartId);
        }

        $mask = $this->quoteIdMaskFactory->create()->load((string)$cartId, 'masked_id');
        if ($mask->getQuoteId()) {
            return $this->cartRepository->getActive((int)$mask->getQuoteId());
        }

        return $this->cartRepository->getActive((int)$cartId);
    }
}
