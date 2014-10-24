<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $installer Magento\Setup\Module\SetupModule */
$installer = $this;

$installer->getConnection()->addColumn(
    $installer->getTable('googleshopping_types'),
    'category',
    array('TYPE' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 'LENGTH' => 40, 'COMMENT' => 'Google product category')
);
