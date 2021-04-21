<?php


namespace Excellence\Geoip\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * @codeCoverageIgnore
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
		$installer = $setup;
		$installer->startSetup();

		/**
		 * Creating table excellence_geoip
		 */
		$table = $installer->getConnection()->newTable(
			$installer->getTable('excellence_geoip')
		)->addColumn(
			'geoip_id',
			\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
			null,
			['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
			'Entity Id'
		)->addColumn(
			'store_id',
			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			255,
			['nullable' => true],
			'Store Id'
		)->addColumn(
			'country_codes',
			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			255,
			['nullable' => true,'default' => null],
			'Country(s)'
		)->addColumn(
			'currency_code',
			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			255,
			['nullable' => true,'default' => null],
			'Currency'
		);
		$installer->getConnection()->createTable($table);
		
        //START table setup
        $table = $installer->getConnection()->newTable(
                    $installer->getTable('excellence_geoip_iplog')
            )->addColumn(
                    'excellence_geoip_iplog_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    [ 'identity' => true, 'nullable' => false, 'primary' => true, 'unsigned' => true, ],
                    'Entity ID'
                )->addColumn(
                    'ip',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    255,
                    [ 'nullable' => false, ],
                    'IP'
                )->addColumn(
                    'country_code',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    255,
                    [ 'nullable' => false, ],
                    'Country Code'
                )->addColumn(
                    'update_time',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                    null,
                    [ 'nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT_UPDATE, ],
                    'Modification Time'
                );
        $installer->getConnection()->createTable($table);
        //END   table setup
        $installer->endSetup();

	}
}