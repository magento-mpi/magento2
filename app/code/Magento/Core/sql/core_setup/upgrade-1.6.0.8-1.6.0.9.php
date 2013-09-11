<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright  {copyright}
 * @license    {license_link}
 */

/* @var $installer \Magento\Core\Model\Resource\Setup */
$installer = $this;

$installer->startSetup();
$connection = $installer->getConnection();

/**
 * Create table 'core_theme_files'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('core_theme_files'))
    ->addColumn('theme_files_id', \Magento\DB\Ddl\Table::TYPE_INTEGER, null, array(
        'identity' => true,
        'unsigned' => true,
        'nullable' => false,
        'primary'  => true,
    ), 'Theme files identifier')
    ->addColumn('theme_id', \Magento\DB\Ddl\Table::TYPE_INTEGER, null, array(
        'nullable' => false,
        'unsigned' => true
    ), 'Theme Id')
    ->addColumn('file_name', \Magento\DB\Ddl\Table::TYPE_TEXT, 255, array('nullable' => false), 'File Name')
    ->addColumn('file_type', \Magento\DB\Ddl\Table::TYPE_TEXT, 32, array('nullable' => false), 'File Type')
    ->addColumn('content', \Magento\DB\Ddl\Table::TYPE_TEXT, \Magento\DB\Ddl\Table::MAX_TEXT_SIZE, array(
        'nullable' => false
    ), 'File Content')
    ->addColumn('order', \Magento\DB\Ddl\Table::TYPE_INTEGER, null, array('nullable' => false, 'default'  => 0), 'Order')
    ->addForeignKey(
        $installer->getFkName('core_theme_files', 'theme_id', 'core_theme', 'theme_id'),
        'theme_id',
        $installer->getTable('core_theme'),
        'theme_id',
        \Magento\DB\Ddl\Table::ACTION_CASCADE,
        \Magento\DB\Ddl\Table::ACTION_CASCADE
    )
    ->setComment('Core theme files');

$installer->getConnection()->createTable($table);

$installer->endSetup();
