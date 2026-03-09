<?php
namespace MyCompany\CustomerBlocklist\Plugin\Checkout;

use Magento\Quote\Api\Data\AddressInterface;
use MyCompany\CustomerBlocklist\Model\Validator;

class PaymentInformationManagementPlugin
{
    private Validator $validator;

    public function __construct(Validator $validator)
    {
        $this->validator = $validator;
    }

    public function beforeSavePaymentInformationAndPlaceOrder($subject, ...$args): array
    {
        if (!isset($args[0])) {
            return $args;
        }

        $cartId = $args[0];
        $guestEmail = null;
        $billingAddress = null;

        if (isset($args[1]) && is_string($args[1])) {
            $guestEmail = $args[1];
            $billingAddress = $args[3] ?? null;
        } else {
            $billingAddress = $args[2] ?? null;
        }

        if (!$billingAddress instanceof AddressInterface) {
            $billingAddress = null;
        }

        $this->validator->validateByCartId($cartId, $billingAddress, $guestEmail);

        return $args;
    }
}
