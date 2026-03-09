<?php
namespace MyCompany\CustomerBlocklist\Plugin\Sales;

use Magento\Framework\AuthorizationInterface;
use Magento\Framework\View\LayoutInterface;
use Magento\Sales\Block\Adminhtml\Order\View;

class OrderViewButtonPlugin
{
    private AuthorizationInterface $authorization;

    public function __construct(AuthorizationInterface $authorization)
    {
        $this->authorization = $authorization;
    }

    public function beforeSetLayout(View $subject, LayoutInterface $layout): void
    {
        $order = $subject->getOrder();
        if (!$order || !$order->getEntityId()) {
            return;
        }

        if (
            !$this->authorization->isAllowed('MyCompany_CustomerBlocklist::add_from_order')
            && !$this->authorization->isAllowed('MyCompany_CustomerBlocklist::config')
        ) {
            return;
        }

        $message = $subject->escapeJs(
            $subject->escapeHtml(__('Add this customer data to the blacklist?'))
        );
        $url = $subject->getUrl('customerblocklist/order/addToBlacklist', ['order_id' => $order->getEntityId()]);

        $subject->addButton(
            'mycompany_customerblocklist_add_to_blacklist',
            [
                'label' => __('Add to Blacklist'),
                'class' => 'action-secondary',
                'onclick' => "confirmSetLocation('{$message}', '{$url}')"
            ],
            0,
            15
        );
    }
}
