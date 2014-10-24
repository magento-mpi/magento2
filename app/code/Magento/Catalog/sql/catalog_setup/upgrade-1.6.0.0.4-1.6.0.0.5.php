<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $installer Magento\Setup\Module\SetupModule */
$installer = $this;

$installer->getConnection()->modifyColumn(
    $installer->getTable('catalog_category_product_index'),
    'position',
    array(
        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
        'unsigned' => false,
        'nullable' => true,
        'default' => null,
        'comment' => 'Position'
    )
);
