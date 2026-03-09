<?php
namespace MyCompany\CustomerBlocklist\Setup\Patch\Schema;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\SchemaPatchInterface;

class CreateCheckoutAttemptLogTable implements SchemaPatchInterface
{
    private ModuleDataSetupInterface $moduleDataSetup;

    public function __construct(ModuleDataSetupInterface $moduleDataSetup)
    {
        $this->moduleDataSetup = $moduleDataSetup;
    }

    public function apply()
    {
        $setup = $this->moduleDataSetup;
        $setup->startSetup();

        $tableName = $setup->getTable('mycompany_customerblocklist_attempt_log');
        if (!$setup->getConnection()->isTableExists($tableName)) {
            $table = $setup->getConnection()->newTable($tableName)
                ->addColumn('entity_id', Table::TYPE_INTEGER, null, ['identity' => true, 'nullable' => false, 'primary' => true, 'unsigned' => true])
                ->addColumn('store_id', Table::TYPE_SMALLINT, null, ['nullable' => true, 'unsigned' => true])
                ->addColumn('quote_id', Table::TYPE_INTEGER, null, ['nullable' => true, 'unsigned' => true])
                ->addColumn('customer_email', Table::TYPE_TEXT, 255, ['nullable' => true])
                ->addColumn('telephone', Table::TYPE_TEXT, 64, ['nullable' => true])
                ->addColumn('firstname', Table::TYPE_TEXT, 255, ['nullable' => true])
                ->addColumn('lastname', Table::TYPE_TEXT, 255, ['nullable' => true])
                ->addColumn('matched_list', Table::TYPE_TEXT, 32, ['nullable' => false])
                ->addColumn('matched_field', Table::TYPE_TEXT, 32, ['nullable' => false])
                ->addColumn('matched_value', Table::TYPE_TEXT, 255, ['nullable' => true])
                ->addColumn('remote_ip', Table::TYPE_TEXT, 64, ['nullable' => true])
                ->addColumn('created_at', Table::TYPE_TIMESTAMP, null, ['nullable' => false, 'default' => Table::TIMESTAMP_INIT])
                ->setComment('MyCompany Customer Blocklist Attempt Log');
            $setup->getConnection()->createTable($table);
        }

        $setup->endSetup();
    }

    public static function getDependencies()
    {
        return [];
    }

    public function getAliases()
    {
        return [];
    }
}
