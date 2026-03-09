<?php
namespace MyCompany\CustomerBlocklist\Block\Adminhtml\Log;

use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use MyCompany\CustomerBlocklist\Model\ResourceModel\AttemptLog\CollectionFactory;

class Index extends Template
{
    protected $_template = 'MyCompany_CustomerBlocklist::log/index.phtml';

    private CollectionFactory $collectionFactory;

    public function __construct(Context $context, CollectionFactory $collectionFactory, array $data = [])
    {
        parent::__construct($context, $data);
        $this->collectionFactory = $collectionFactory;
    }

    public function getLogs(): array
    {
        $collection = $this->collectionFactory->create();
        $collection->setOrder('created_at', 'DESC');
        
        $page = (int)$this->getRequest()->getParam('p', 1);
        $pageSize = (int)$this->getRequest()->getParam('limit', 50);
        
        if ($pageSize < 10) {
            $pageSize = 10;
        }
        if ($pageSize > 500) {
            $pageSize = 500;
        }
        
        $collection->setPageSize($pageSize);
        $collection->setCurPage($page);
        
        return $collection->getItems();
    }
    
    public function getTotalCount(): int
    {
        $collection = $this->collectionFactory->create();
        return $collection->getSize();
    }
    
    public function getCurrentPage(): int
    {
        return (int)$this->getRequest()->getParam('p', 1);
    }
    
    public function getPageSize(): int
    {
        $pageSize = (int)$this->getRequest()->getParam('limit', 50);
        if ($pageSize < 10) {
            return 10;
        }
        if ($pageSize > 500) {
            return 500;
        }
        return $pageSize;
    }
}
