<?php
namespace MyCompany\CustomerBlocklist\Model\ResourceModel\AttemptLog;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use MyCompany\CustomerBlocklist\Model\AttemptLog;
use MyCompany\CustomerBlocklist\Model\ResourceModel\AttemptLog as AttemptLogResource;

class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init(AttemptLog::class, AttemptLogResource::class);
    }
}
