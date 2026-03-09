<?php
namespace MyCompany\CustomerBlocklist\Setup\Patch\Schema;

use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\SchemaPatchInterface;

class AddIndexesToAttemptLogTable implements SchemaPatchInterface
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

        $connection = $setup->getConnection();
        $tableName = $setup->getTable('mycompany_customerblocklist_attempt_log');

        if ($connection->isTableExists($tableName)) {
            $indexes = $connection->getIndexList($tableName);

            $indexName = $connection->getIndexName($tableName, ['created_at']);
            if (!array_key_exists($indexName, $indexes)) {
                $connection->addIndex(
                    $tableName,
                    $indexName,
                    ['created_at']
                );
            }

            $indexName = $connection->getIndexName($tableName, ['store_id']);
            if (!array_key_exists($indexName, $indexes)) {
                $connection->addIndex(
                    $tableName,
                    $indexName,
                    ['store_id']
                );
            }

            $indexName = $connection->getIndexName($tableName, ['customer_email']);
            if (!array_key_exists($indexName, $indexes)) {
                $connection->addIndex(
                    $tableName,
                    $indexName,
                    ['customer_email']
                );
            }
        }

        $setup->endSetup();
    }

    public static function getDependencies()
    {
        return [
            \MyCompany\CustomerBlocklist\Setup\Patch\Schema\CreateCheckoutAttemptLogTable::class,
        ];
    }

    public function getAliases()
    {
        return [];
    }
}
