<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

$installer = $this;
/** @var $installer \Magento\Catalog\Model\Resource\Setup */
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
