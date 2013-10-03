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
$installer = $this;

/** @var $attribute \Magento\Catalog\Model\Resource\Eav\Attribute */
$attribute = $installer->getAttribute('catalog_product', 'weight');

if ($attribute) {
    $installer->updateAttribute($attribute['entity_type_id'], $attribute['attribute_id'],
        'frontend_input',  $attribute['attribute_code']);
}
