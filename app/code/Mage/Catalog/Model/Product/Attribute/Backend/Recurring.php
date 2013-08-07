<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Backend for recurring profile parameter
 */
class Mage_Catalog_Model_Product_Attribute_Backend_Recurring
extends Mage_Eav_Model_Entity_Attribute_Backend_Serialized
{
    /**
     * Serialize or remove before saving
     * @param Mage_Catalog_Model_Product $product
     */
    public function beforeSave($product)
    {
        if ($product->hasIsRecurring()) {
            if ($product->isRecurring()) {
                parent::beforeSave($product);
            } else {
                $product->unsRecurringProfile();
            }
        }
    }

    /**
     * Unserialize or remove on failure
     * @param Mage_Catalog_Model_Product $product
     */
    protected function _unserialize(Magento_Object $product)
    {
        if ($product->hasIsRecurring()) {
            if ($product->isRecurring()) {
                parent::_unserialize($product);
            } else {
                $product->unsRecurringProfile();
            }
        }
    }
}
