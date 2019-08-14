<?php
namespace LOANDT\Testimonial\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;

/**
 * @codeCoverageIgnore
 */

class UpgradeSchema implements UpgradeSchemaInterface
{
	public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
	{
		$installer = $setup;

		$installer->startSetup();

		if (version_compare($context->getVersion(), '1.0.1', '<')) {
			if (!$installer->tableExists('testimonial')) {
				$table = $installer->getConnection()->newTable(
					$installer->getTable('testimonial')
				)
                ->addColumn(
                    'testimonial_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                    'Testimonial ID'
                )
                ->addColumn(
                    'name',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    100,
                    ['nullable' => false],
                    'Name'
                )
                ->addColumn(
                    'designation',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    100,
                    ['nullable' => false],
                    'Designation'
                )
                ->addColumn(
                    'message',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    100,
                    ['nullable' => false],
                    'Message'
                )
                ->addColumn(
                    'email',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    100,
                    ['nullable' => false],
                    'Email'
                )
                ->addColumn(
                    'contact',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    100,
                    ['nullable' => false],
                    'Contact'
                )
                ->addColumn(
                    'image',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    100,
                    ['nullable' => false],
                    'Image'
                )
                ->addColumn(
                    'status',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    1,
                    ['nullable' => false, 'default' => 0],
                    'Status'
                )
				->setComment('Testimonial Table');
				$installer->getConnection()->createTable($table);

				$installer->getConnection()->addIndex(
                $installer->getTable('testimonial'),
                $setup->getIdxName(
                    $installer->getTable('testimonial'),
                    ['testimonial_id', 'name', 'designation', 'message', 'email', 'contact', 'image', 'status'],
                    \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_FULLTEXT
                ),
                ['testimonial_id', 'name', 'designation', 'message', 'email', 'contact', 'image', 'status'],
                \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_FULLTEXT
				);
			}
		}

		$installer->endSetup();
	}
}