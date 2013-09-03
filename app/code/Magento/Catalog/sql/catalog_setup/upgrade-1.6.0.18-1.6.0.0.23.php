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

$installer->updateAttribute(
    Magento_Catalog_Model_Product::ENTITY,
    'image',
    'used_in_product_listing',
    '1'
);
