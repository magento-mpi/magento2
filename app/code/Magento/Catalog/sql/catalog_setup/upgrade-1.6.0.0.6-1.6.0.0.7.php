<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $installer Magento_Catalog_Model_Resource_Setup */
$installer = $this;

$installer->getConnection()->addColumn(
    $installer->getTable('catalog_category_anc_products_index_tmp'),
    'position',
    array(
        'type'      => \Magento\DB\Ddl\Table::TYPE_INTEGER,
        'unsigned'  => true,
        'nullable'  => true,
        'comment'   => 'Position'
    )
);