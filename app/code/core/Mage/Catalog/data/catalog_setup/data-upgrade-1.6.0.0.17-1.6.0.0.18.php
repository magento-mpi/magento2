<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $this Mage_Catalog_Model_Resource_Setup */

$attribute = $this->getAttribute(Mage_Catalog_Model_Product::ENTITY, 'category_ids');

if ($attribute) {
    $this->updateAttribute($attribute['entity_type_id'], $attribute['attribute_id'],
        'backend_model', 'Mage_Catalog_Model_Product_Attribute_Backend_Category'
    );
}
