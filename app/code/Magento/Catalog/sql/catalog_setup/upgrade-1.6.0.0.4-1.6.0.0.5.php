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
/** @var $installer Magento_Catalog_Model_Resource_Setup */
$installer->getConnection()->modifyColumn(
    $installer->getTable('catalog_category_product_index'),
    'position',
    array(
        'type'      => Magento_DB_Ddl_Table::TYPE_INTEGER,
        'unsigned'  => false,
        'nullable'  => true,
        'default'   => null,
        'comment'   => 'Position'
    )
);
