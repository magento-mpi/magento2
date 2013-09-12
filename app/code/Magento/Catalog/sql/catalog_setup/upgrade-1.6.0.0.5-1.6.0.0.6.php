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
    'url_key',
    'frontend_label',
    'URL Key'
);

$installer->updateAttribute(
    Magento_Catalog_Model_Category::ENTITY,
    'url_key',
    'frontend_label',
    'URL Key'
);

$installer->updateAttribute(
    Magento_Catalog_Model_Product::ENTITY,
    'options_container',
    'frontend_label',
    'Display Product Options In'
);
