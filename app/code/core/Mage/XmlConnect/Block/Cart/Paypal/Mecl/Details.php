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
 * PayPal MECL Shopping cart details xml renderer
 *
 * @category    Mage
 * @package     Mage_Xmlconnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Block_Cart_Paypal_Mecl_Details extends Mage_Paypal_Block_Express_Review_Details
{
    /**
     * Add cart details to XML object
     *
     * @param Mage_XmlConnect_Model_Simplexml_Element $reviewXmlObj
     * @return Mage_XmlConnect_Model_Simplexml_Element
     */
    public function addDetailsToXmlObj(Mage_XmlConnect_Model_Simplexml_Element $reviewXmlObj)
    {
        $itemsXmlObj = $reviewXmlObj->addChild('ordered_items');
        foreach ($this->getItems() as $_item) {
            $this->getItemXml($_item, $itemsXmlObj);
        }

        $reviewXmlObj->appendChild($this->getChildBlock('totals')->setReturnObjectFlag(true)->_toHtml());

        return $reviewXmlObj;
    }

    /**
     * Get item row xml
     *
     * @param Mage_Sales_Model_Quote_Item $item
     * @param Mage_XmlConnect_Model_Simplexml_Element $reviewXmlObj
     * @return Mage_XmlConnect_Model_Simplexml_Element
     */
    public function getItemXml(
        Mage_Sales_Model_Quote_Item $item,
        Mage_XmlConnect_Model_Simplexml_Element $reviewXmlObj
    )
    {
        $renderer = $this->getItemRenderer($item->getProductType())->setItem($item)->setQuote($this->getQuote());
        return $renderer->addProductToXmlObj($reviewXmlObj);
    }

    /**
     * Add renderer for item product type
     *
     * @param string $productType
     * @param string $blockType
     * @param string $template
     * @return Mage_Checkout_Block_Cart_Abstract
     */
    public function addItemRender($productType, $blockType, $template)
    {
        $this->_itemRenders[$productType] = array(
            'block' => $blockType,
            'template' => $template,
            'blockInstance' => null
        );
        return $this;
    }
}
