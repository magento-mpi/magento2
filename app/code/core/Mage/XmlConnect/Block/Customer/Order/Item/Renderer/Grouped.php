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
 * Customer order item xml renderer for grouped product type
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Block_Customer_Order_Item_Renderer_Grouped extends Mage_Sales_Block_Order_Item_Renderer_Grouped
{
    /**
     * Default product type
     */
    const DEFAULT_PRODUCT_TYPE = 'default';

    /**
     * Add item to XML object
     * (get from template: Mage_Sales::order/items/renderer/default.phtml)
     *
     * @param Mage_XmlConnect_Model_Simplexml_Element $orderItemXmlObj
     * @return null
     */
    public function addItemToXmlObject(Mage_XmlConnect_Model_Simplexml_Element $orderItemXmlObj)
    {
        if (!($item = $this->getItem()->getOrderItem())) {
            $item = $this->getItem();
        }
        if (!($productType = $item->getRealProductType())) {
            $productType = self::DEFAULT_PRODUCT_TYPE;
        }
        $renderer = $this->getRenderedBlock()->getItemRenderer($productType);
        $renderer->setItem($this->getItem());
        $renderer->addItemToXmlObject($orderItemXmlObj);
    }
}
