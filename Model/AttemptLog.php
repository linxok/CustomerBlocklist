<?php
namespace MyCompany\CustomerBlocklist\Model;

use Magento\Framework\Model\AbstractModel;
use MyCompany\CustomerBlocklist\Model\ResourceModel\AttemptLog as AttemptLogResource;

class AttemptLog extends AbstractModel
{
    protected function _construct()
    {
        $this->_init(AttemptLogResource::class);
    }
}
