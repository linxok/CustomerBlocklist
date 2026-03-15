<?php
namespace MyCompany\CustomerBlocklist\Controller\Adminhtml\Log;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\App\Filesystem\DirectoryList;
use MyCompany\CustomerBlocklist\Model\ResourceModel\AttemptLog\CollectionFactory;

class ExportCsv extends Action
{
    public const ADMIN_RESOURCE = 'MyCompany_CustomerBlocklist::logs';

    private FileFactory $fileFactory;
    private CollectionFactory $collectionFactory;

    public function __construct(
        Context $context,
        FileFactory $fileFactory,
        CollectionFactory $collectionFactory
    ) {
        parent::__construct($context);
        $this->fileFactory = $fileFactory;
        $this->collectionFactory = $collectionFactory;
    }

    public function execute()
    {
        $fileName = 'blocklist_logs_' . date('Y-m-d_H-i-s') . '.csv';
        $collection = $this->collectionFactory->create();
        $collection->setOrder('created_at', 'DESC');

        $content = "Date,Store ID,Quote ID,Email,Telephone,First Name,Last Name,Matched List,Matched Field,Matched Value,IP\n";

        foreach ($collection as $log) {
            $content .= sprintf(
                '"%s","%s","%s","%s","%s","%s","%s","%s","%s","%s","%s"' . "\n",
                $this->escapeCsv($log->getData('created_at')),
                $this->escapeCsv($log->getData('store_id')),
                $this->escapeCsv($log->getData('quote_id')),
                $this->escapeCsv($log->getData('customer_email')),
                $this->escapeCsv($log->getData('telephone')),
                $this->escapeCsv($log->getData('firstname')),
                $this->escapeCsv($log->getData('lastname')),
                $this->escapeCsv($log->getData('matched_list')),
                $this->escapeCsv($log->getData('matched_field')),
                $this->escapeCsv($log->getData('matched_value')),
                $this->escapeCsv($log->getData('remote_ip'))
            );
        }

        return $this->fileFactory->create(
            $fileName,
            $content,
            DirectoryList::VAR_DIR,
            'text/csv'
        );
    }

    private function escapeCsv($value): string
    {
        return str_replace('"', '""', (string)$value);
    }
}
