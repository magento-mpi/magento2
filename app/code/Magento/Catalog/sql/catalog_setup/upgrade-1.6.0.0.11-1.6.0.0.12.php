<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $installer Magento\Setup\Module\SetupModule */
$installer = $this;
$indexFields = array('website_id', 'customer_group_id', 'min_price');
$installer->getConnection()->addIndex(
    $installer->getTable('catalog_product_index_price'),
    $installer->getIdxName('catalog_product_index_price', $indexFields),
    $indexFields
);
