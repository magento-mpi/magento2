<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Grouped product options xml renderer
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Block_Catalog_Product_Options_Grouped extends Mage_XmlConnect_Block_Catalog_Product_Options
{
    /**
     * Generate bundle product options xml
     *
     * @param Mage_Catalog_Model_Product $product
     * @param bool $isObject
     * @return string | Mage_XmlConnect_Model_Simplexml_Element
     */
    public function getProductOptionsXml(Mage_Catalog_Model_Product $product, $isObject = false)
    {
        $xmlModel = Mage::getModel('Mage_XmlConnect_Model_Simplexml_Element', array('data' => '<product></product>'));
        $optionsNode = $xmlModel->addChild('options');

        if (!$product->getId()) {
            return $isObject ? $xmlModel : $xmlModel->asNiceXml();
        }
        $xmlModel->addAttribute('id', $product->getId());
        if (!$product->isSaleable()) {
            return $isObject ? $xmlModel : $xmlModel->asNiceXml();
        }
        /**
         * Grouped (associated) products
         */
        $_associatedProducts = $product->getTypeInstance()->getAssociatedProducts($product);
        if (!sizeof($_associatedProducts)) {
            return $isObject ? $xmlModel : $xmlModel->asNiceXml();
        }

        foreach ($_associatedProducts as $_item) {
            if (!$_item->isSaleable()) {
                continue;
            }
            $optionNode = $optionsNode->addChild('option');

            $optionNode->addAttribute('code', 'super_group[' . $_item->getId() . ']');
            $optionNode->addAttribute('type', 'product');
            $optionNode->addAttribute('label', $xmlModel->escapeXml($_item->getName()));
            $optionNode->addAttribute('is_qty_editable', 1);
            $optionNode->addAttribute('qty', $_item->getQty()*1);

            /**
             * Process product price
             */
            if ($_item->getPrice() != $_item->getFinalPrice()) {
                $productPrice = $_item->getFinalPrice();
            } else {
                $productPrice = $_item->getPrice();
            }

            if ($productPrice != 0) {
                $productPrice = Mage::helper('Mage_XmlConnect_Helper_Data')->formatPriceForXml($productPrice);
                $optionNode->addAttribute('price', Mage::helper('Mage_XmlConnect_Helper_Data')->formatPriceForXml(
                    Mage::helper('Mage_Core_Helper_Data')->currency($productPrice, false, false)
                ));
                $optionNode->addAttribute('formated_price', $this->_formatPriceString($productPrice, $product));
            }
        }

        return $isObject ? $xmlModel : $xmlModel->asNiceXml();
    }
}
