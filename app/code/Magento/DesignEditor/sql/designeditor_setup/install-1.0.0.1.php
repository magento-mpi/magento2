<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_DesignEditor
 * @copyright  {copyright}
 * @license    {license_link}
 */

/* @var $installer Magento_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();
$connection = $installer->getConnection();
/**
 * Modifying 'core_layout_update' table. Adding 'is_vde' column
 */
$tableCoreLayoutLink = $installer->getTable('core_layout_update');

$connection->addColumn($tableCoreLayoutLink, 'is_vde',
    array(
        'type'     => \Magento\DB\Ddl\Table::TYPE_BOOLEAN,
        'nullable' => false,
        'default'  => '0',
        'comment'  => 'Defines whether layout update created via design editor'
    )
);

$installer->endSetup();
