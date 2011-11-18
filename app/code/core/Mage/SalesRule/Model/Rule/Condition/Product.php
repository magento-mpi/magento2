<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_SalesRule
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Mage_SalesRule_Model_Rule_Condition_Product extends Mage_CatalogRule_Model_Rule_Condition_Product
{

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
     * @param Varien_Object $object
     * @return bool
     */
    public function validate(Varien_Object $object)
    {
        $product = false;
        if ($object->getProduct() instanceof Mage_Catalog_Model_Product) {
            $product = $object->getProduct();
        } else {
            $product = Mage::getModel('Mage_Catalog_Model_Product')
                ->load($object->getProductId());
        }

        $product
            ->setQuoteItemQty($object->getQty())
            ->setQuoteItemPrice($object->getPrice()) // possible bug: need to use $object->getBasePrice()
            ->setQuoteItemRowTotal($object->getBaseRowTotal());

        return parent::validate($product);
    }
}
