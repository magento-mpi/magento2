<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $installer \Magento\Catalog\Model\Resource\Setup */
$installer  = $this;
$indexFields = array('website_id', 'customer_group_id', 'min_price');
$installer->getConnection()->addIndex(
    $installer->getTable('catalog_product_index_price'),
    $installer->getIdxName('catalog_product_index_price', $indexFields),
    $indexFields
);
