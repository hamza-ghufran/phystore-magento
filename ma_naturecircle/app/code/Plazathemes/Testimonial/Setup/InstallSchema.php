<?php
/**
* Copyright © 2015 PlazaThemes.com. All rights reserved.

* @author PlazaThemes Team <contact@plazathemes.com>
*/

namespace Plazathemes\Testimonial\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * @codeCoverageIgnore
 */
class InstallSchema implements InstallSchemaInterface {
	/**
	 * {@inheritdoc}
	 */
	public function install(SchemaSetupInterface $setup, ModuleContextInterface $context) {
		$installer = $setup;

		$installer->startSetup();

		/**
		 * Drop tables if exists
		 */
		$installer->getConnection()->dropTable($installer->getTable('pt_testimonial'));

		/**
		 * Create table pt_testimonial
		 */
		$table = $installer->getConnection()->newTable(
			$installer->getTable('pt_testimonial')
		)->addColumn(
			'testimo_id',
			\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
			10,
			['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
			'Testimonial ID'
		)->addColumn(
			'name',
			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			255,
			['nullable' => false, 'default' => ''],
			'Name'
		)->addColumn(
			'store_id',
			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			255,
			['nullable' => false, 'default' => ''],
			'Store Id'
		)->addColumn(
			'email',
			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			255,
			['nullable' => true],
			'Email'
		)->addColumn(
			'avatar',
			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			255,
			['nullable' => true],
			'Avatar'
		)->addColumn(
			'website',
			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			255,
			['nullable' => true],
			'Website'
		)->addColumn(
			'company',
			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			255,
			['nullable' => true],
			'Company'
		)->addColumn(
			'address',
			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			255,
			['nullable' => true],
			'Address'
		)->addColumn(
			'job',
			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			255,
			['nullable' => true],
			'Job'
		)->addColumn(
			'testimonial',
			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			255,
			['nullable' => true],
			'Testimonial'
		)->addColumn(
			'created_time',
			\Magento\Framework\DB\Ddl\Table::TYPE_DATE,
			null,
			[],
			'Created Time'
		)->addColumn(
			'update_time',
			\Magento\Framework\DB\Ddl\Table::TYPE_DATE,
			null,
			[],
			'Update Time'
		)->addColumn(
			'status',
			\Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
			6,
			['nullable' => false, 'default' => '1'],
			'Status'
		)->addColumn(
			'order',
			\Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
			null,
			['unsigned' => true, 'nullable' => false, 'default' => '0'],
			'Order'
		);
		$installer->getConnection()->createTable($table);
		/**
		 * End create table pt_testimonial
		 */

		$installer->endSetup();

	}
}
