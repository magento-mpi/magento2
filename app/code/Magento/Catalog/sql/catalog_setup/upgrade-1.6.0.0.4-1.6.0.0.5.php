<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */

$installer = $this;
/** @var $installer \Magento\Catalog\Model\Resource\Setup */
$installer->getConnection()->modifyColumn(
    $installer->getTable('catalog_category_product_index'),
    'position',
    array(
        'type'      => \Magento\DB\Ddl\Table::TYPE_INTEGER,
        'unsigned'  => false,
        'nullable'  => true,
        'default'   => null,
        'comment'   => 'Position'
    )
);
