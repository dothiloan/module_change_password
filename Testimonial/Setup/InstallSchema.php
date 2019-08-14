<?php
namespace LOANDT\Testimonial\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * @codeCoverageIgnore
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @throws \Zend_Db_Exception
     */

    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        /**
         * Create table 'pfay_contacts'
         */

        if (!$setup->getConnection()->isTableExists($setup->getTable('testimonial'))) {
            $table = $setup->getConnection()
            ->newTable($setup->getTable('testimonial'))
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
            ->setComment('Testimonial Table')
            ->setOption('type', 'InnoDB')
            ->setOption('charset', 'utf8');

            $setup->getConnection()->createTable($table);
        }
        $setup->endSetup();
    }
}