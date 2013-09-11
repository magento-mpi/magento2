<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_SalesRule
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Product rule condition data model
 *
 * @category Magento
 * @package Magento_SalesRule
 * @author Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\SalesRule\Model\Rule\Condition;

class Product extends \Magento\Rule\Model\Condition\Product\AbstractProduct
{
    /**
     * Add special attributes
     *
     * @param array $attributes
     */
    protected function _addSpecialAttributes(array &$attributes)
    {
        parent::_addSpecialAttributes($attributes);
        $attributes['quote_item_qty'] = __('Quantity in cart');
        $attributes['quote_item_price'] = __('Price in cart');
        $attributes['quote_item_row_total'] = __('Row total in cart');
    }

    /**
     * Validate Product Rule Condition
     *
     * @param \Magento\Object $object
     *
     * @return bool
     */
    public function validate(\Magento\Object $object)
    {
        /** @var \Magento\Catalog\Model\Product $product */
        $product = $object->getProduct();
        if (!($product instanceof \Magento\Catalog\Model\Product)) {
            $product = \Mage::getModel('Magento\Catalog\Model\Product')->load($object->getProductId());
        }

        $product
            ->setQuoteItemQty($object->getQty())
            ->setQuoteItemPrice($object->getPrice()) // possible bug: need to use $object->getBasePrice()
            ->setQuoteItemRowTotal($object->getBaseRowTotal());

        $valid = parent::validate($product);
        if (!$valid && $product->getTypeId() == \Magento\Catalog\Model\Product\Type\Configurable::TYPE_CODE) {
            $children = $object->getChildren();
            $valid = $children && $this->validate($children[0]);
        }

        return $valid;
    }
}
