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
class Magento_SalesRule_Model_Rule_Condition_Product extends Magento_Rule_Model_Condition_Product_Abstract
{
    /**
     * @var Magento_Catalog_Model_ProductFactory
     */
    protected $_productFactory;

    /**
     * @param Magento_Backend_Helper_Data $backendData
     * @param Magento_Rule_Model_Condition_Context $context
     * @param Magento_Eav_Model_Config $config
     * @param Magento_Catalog_Model_Product $product
     * @param Magento_Catalog_Model_Resource_Product $productResource
     * @param Magento_Eav_Model_Resource_Entity_Attribute_Set_Collection $attrSetCollection
     * @param Magento_Catalog_Model_ProductFactory $productFactory
     * @param array $data
     */
    public function __construct(
        Magento_Backend_Helper_Data $backendData,
        Magento_Rule_Model_Condition_Context $context,
        Magento_Eav_Model_Config $config,
        Magento_Catalog_Model_Product $product,
        Magento_Catalog_Model_Resource_Product $productResource,
        Magento_Eav_Model_Resource_Entity_Attribute_Set_Collection $attrSetCollection,
        Magento_Catalog_Model_ProductFactory $productFactory,
        array $data = array()
    ) {
        parent::__construct($backendData, $context, $config, $product, $productResource, $attrSetCollection, $data);
        $this->_productFactory = $productFactory;
    }

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
     * @param Magento_Object $object
     *
     * @return bool
     */
    public function validate(Magento_Object $object)
    {
        /** @var Magento_Catalog_Model_Product $product */
        $product = $object->getProduct();
        if (!($product instanceof Magento_Catalog_Model_Product)) {
            $product = $this->_productFactory->create()->load($object->getProductId());
        }

        $product->setQuoteItemQty($object->getQty())
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
