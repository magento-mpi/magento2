<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_SalesRule
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Product rule condition data model
 *
 * @category Mage
 * @package Mage_SalesRule
 * @author Magento Core Team <core@magentocommerce.com>
 */
class Mage_SalesRule_Model_Rule_Condition_Product extends Mage_Rule_Model_Condition_Product_Abstract
{
    /**
     * Add special attributes
     *
     * @param array $attributes
     */
    protected function _addSpecialAttributes(array &$attributes)
    {
        parent::_addSpecialAttributes($attributes);
        $attributes['quote_item_qty'] = Mage::helper('Mage_SalesRule_Helper_Data')->__('Quantity in cart');
        $attributes['quote_item_price'] = Mage::helper('Mage_SalesRule_Helper_Data')->__('Price in cart');
        $attributes['quote_item_row_total'] = Mage::helper('Mage_SalesRule_Helper_Data')->__('Row total in cart');
    }

    /**
     * Validate Product Rule Condition
     *
     * @param Magento_Object $object
     *
     * @return bool
     */
    public function validate(Magento_Object $object)
    {
        /** @var Magento_Catalog_Model_Product $product */
        $product = $object->getProduct();
        if (!($product instanceof Magento_Catalog_Model_Product)) {
            $product = Mage::getModel('Magento_Catalog_Model_Product')->load($object->getProductId());
        }

        $product
            ->setQuoteItemQty($object->getQty())
            ->setQuoteItemPrice($object->getPrice()) // possible bug: need to use $object->getBasePrice()
            ->setQuoteItemRowTotal($object->getBaseRowTotal());

        $valid = parent::validate($product);
        if (!$valid && $product->getTypeId() == Magento_Catalog_Model_Product_Type_Configurable::TYPE_CODE) {
            $children = $object->getChildren();
            $valid = $children && $this->validate($children[0]);
        }

        return $valid;
    }
}
