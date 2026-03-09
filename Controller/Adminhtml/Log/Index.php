<?php
namespace MyCompany\CustomerBlocklist\Controller\Adminhtml\Log;

use Magento\Backend\App\Action;
use Magento\Framework\Controller\ResultFactory;

class Index extends Action
{
    public const ADMIN_RESOURCE = 'MyCompany_CustomerBlocklist::logs';

    public function execute()
    {
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->setActiveMenu('MyCompany_CustomerBlocklist::logs');
        $resultPage->getConfig()->getTitle()->prepend(__('Customer Blocklist Logs'));
        return $resultPage;
    }
}
