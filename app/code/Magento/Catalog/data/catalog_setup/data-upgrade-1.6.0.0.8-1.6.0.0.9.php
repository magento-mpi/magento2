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

/** @var $attribute Magento_Catalog_Model_Resource_Eav_Attribute */
$attribute = $installer->getAttribute('catalog_product', 'weight');

if ($attribute) {
    $installer->updateAttribute($attribute['entity_type_id'], $attribute['attribute_id'],
        'frontend_input',  $attribute['attribute_code']);
}
