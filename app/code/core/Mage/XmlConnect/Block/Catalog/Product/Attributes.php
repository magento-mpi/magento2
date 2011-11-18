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
 * Product additional attributes xml renderer
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Block_Catalog_Product_Attributes extends Mage_Catalog_Block_Product_View_Attributes
{
    /**
     * Add additional information (attributes) to current product xml object
     *
     * @param Mage_Catalog_Model_Product $product
     * @param Mage_XmlConnect_Model_Simplexml_Element $productXmlObject
     */
    public function addAdditionalData(
        Mage_Catalog_Model_Product $product, Mage_XmlConnect_Model_Simplexml_Element $productXmlObject
    ) {
        if ($product && $productXmlObject && $product->getId()) {
            $this->_product = $product;
            $additionalData = $this->getAdditionalData();
            if (!empty($additionalData)) {
                $attributesXmlObj = $productXmlObject->addChild('additional_attributes');
                foreach ($additionalData as $data) {
                    $attribute = Mage::helper('Mage_Catalog_Helper_Output')
                        ->productAttribute($product, $data['value'], $data['code']);
                    /** @var $attrXmlObject Mage_XmlConnect_Model_Simplexml_Element */
                    $attrXmlObject = $attributesXmlObj->addChild('item');
                    $attrXmlObject->addCustomChild('label', $data['label']);
                    $attrXmlObject->addCustomChild('value', $attribute);
                }
            }
        }
    }
}
