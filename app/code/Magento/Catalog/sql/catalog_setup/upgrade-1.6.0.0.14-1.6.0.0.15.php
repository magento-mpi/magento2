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

$installer->installEntities();

foreach (array('news_from_date', 'custom_design_from') as $attributeCode) {
    $installer->updateAttribute(
        Magento_Catalog_Model_Product::ENTITY,
        $attributeCode,
        'backend_model',
        'Magento_Catalog_Model_Product_Attribute_Backend_Startdate'
    );
}
