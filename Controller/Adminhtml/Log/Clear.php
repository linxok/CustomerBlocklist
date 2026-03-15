<?php
namespace MyCompany\CustomerBlocklist\Controller\Adminhtml\Log;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use MyCompany\CustomerBlocklist\Model\ResourceModel\AttemptLog\CollectionFactory;

class Clear extends Action
{
    public const ADMIN_RESOURCE = 'MyCompany_CustomerBlocklist::logs';

    private CollectionFactory $collectionFactory;

    public function __construct(Context $context, CollectionFactory $collectionFactory)
    {
        parent::__construct($context);
        $this->collectionFactory = $collectionFactory;
    }

    public function execute()
    {
        try {
            $daysToKeep = (int)$this->getRequest()->getParam('days', 0);
            
            if ($daysToKeep < 0 || $daysToKeep > 3650) {
                throw new \InvalidArgumentException(__('Invalid days parameter.'));
            }
            
            $collection = $this->collectionFactory->create();
            $count = $collection->getSize();

            if ($daysToKeep > 0) {
                $dateLimit = date('Y-m-d H:i:s', strtotime("-{$daysToKeep} days"));
                $collection->addFieldToFilter('created_at', ['lt' => $dateLimit]);
                $count = $collection->getSize();
            }

            if ($count > 0) {
                foreach ($collection as $log) {
                    $log->delete();
                }
                
                if ($daysToKeep > 0) {
                    $message = __('Deleted %1 log entries older than %2 days.', $count, $daysToKeep);
                } else {
                    $message = __('Deleted all %1 log entries.', $count);
                }
                $this->messageManager->addSuccessMessage($message);
            } else {
                $this->messageManager->addNoticeMessage(__('No log entries found to delete.'));
            }
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__('Error clearing logs: %1', $e->getMessage()));
        }

        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('*/*/index');
    }
}
