<?php
/**
 * {license_notice}
 *
 * @category   Mage
 * @package    Mage_LoadTest
 * @copyright  {copyright}
 * @license    {license_link}
 */

class Mage_LoadTest_Model_Renderer_Sales_Item_Type_Configurable
    extends Mage_LoadTest_Model_Renderer_Sales_Item_Type_Abstract
{

    public function prepareRequestForCart($product)
    {
        $this->_product = $product;
        $typeInstance = $this->_product->getTypeInstance();

        $request = array();
        $allProducts = $typeInstance->getUsedProducts($this->_product);
        if (count($allProducts) == 0) {
            return $request;
        }

        $request['qty'] = $this->_getAllowedQty();
        $options = array();
        foreach ($allProducts as $product) {
            if ($product->isSaleable()) {
                $productId = $product->getId();
                foreach ($typeInstance->getConfigurableAttributes($this->_product) as $attribute) {
                    $productAttribute = $attribute->getProductAttribute();
                    $attributeValue = $product->getData($productAttribute->getAttributeCode());
                    if (!isset($options[$productId])) {
                        $options[$productId] = array();
                    }
                    $options[$productId][$productAttribute->getId()] = $attributeValue;
                }
            }
        }

        $request['super_attribute'] = $options[array_rand($options, 1)];
        return new Varien_Object($request);
    }
}

