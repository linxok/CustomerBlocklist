<?php
namespace MyCompany\CustomerBlocklist\Plugin\Sales;

use Magento\Framework\Exception\LocalizedException;
use Magento\Payment\Helper\Data as PaymentHelper;
use Magento\Payment\Model\Method\Substitution;
use Magento\Sales\Model\Order\Payment\Info;
use UnexpectedValueException;

class OrderPaymentInfoPlugin
{
    private PaymentHelper $paymentHelper;

    public function __construct(PaymentHelper $paymentHelper)
    {
        $this->paymentHelper = $paymentHelper;
    }

    public function aroundGetMethodInstance(Info $subject, callable $proceed)
    {
        try {
            return $proceed();
        } catch (LocalizedException|UnexpectedValueException $e) {
            $instance = $this->paymentHelper->getMethodInstance(Substitution::CODE);
            $instance->setInfoInstance($subject);
            if ($subject->getOrder()) {
                $instance->setStore($subject->getOrder()->getStoreId());
            }
            $subject->setMethodInstance($instance);
            return $instance;
        }
    }
}
