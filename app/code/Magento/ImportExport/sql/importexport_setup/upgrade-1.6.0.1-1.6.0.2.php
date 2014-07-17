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
    $installer->getTable('importexport_importdata'),
    'data',
    array('type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 'length' => '4G', 'default' => false, 'comment' => 'Data')
);
