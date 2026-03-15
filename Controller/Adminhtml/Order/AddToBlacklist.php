<?php
namespace MyCompany\CustomerBlocklist\Controller\Adminhtml\Order;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Api\OrderRepositoryInterface;
use MyCompany\CustomerBlocklist\Model\AddOrderToBlacklist;

class AddToBlacklist extends Action
{
    public const ADMIN_RESOURCE = 'MyCompany_CustomerBlocklist::add_from_order';

    private OrderRepositoryInterface $orderRepository;
    private AddOrderToBlacklist $addOrderToBlacklist;

    public function __construct(
        Context $context,
        OrderRepositoryInterface $orderRepository,
        AddOrderToBlacklist $addOrderToBlacklist
    ) {
        parent::__construct($context);
        $this->orderRepository = $orderRepository;
        $this->addOrderToBlacklist = $addOrderToBlacklist;
    }

    public function execute()
    {
        $orderId = (int)$this->getRequest()->getParam('order_id');
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        if ($orderId <= 0) {
            $this->messageManager->addErrorMessage(__('Order ID is missing.'));
            return $resultRedirect->setPath('sales/order/index');
        }

        try {
            $order = $this->orderRepository->get($orderId);
            $added = $this->addOrderToBlacklist->execute($order);

            if ($added) {
                $this->messageManager->addSuccessMessage(__('Customer data from order #%1 was added to the blacklist.', $order->getIncrementId()));
            } else {
                $this->messageManager->addNoticeMessage(__('Customer data from order #%1 is already in the blacklist.', $order->getIncrementId()));
            }
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__('Unable to add customer data from the order to the blacklist.'));
        }

        return $resultRedirect->setPath('sales/order/view', ['order_id' => $orderId]);
    }
}
