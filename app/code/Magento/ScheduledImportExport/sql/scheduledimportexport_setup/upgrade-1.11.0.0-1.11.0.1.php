<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $installer \Magento\Framework\Module\Setup */
$installer = $this;

$installer->getConnection()->modifyColumn(
    $installer->getTable('magento_scheduled_operations'),
    'force_import',
    array('type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT, 'nullable' => false, 'default' => '0')
);
