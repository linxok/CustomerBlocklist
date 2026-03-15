<?php
namespace MyCompany\CustomerBlocklist\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class AttemptLog extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('mycompany_customerblocklist_attempt_log', 'entity_id');
    }
}
