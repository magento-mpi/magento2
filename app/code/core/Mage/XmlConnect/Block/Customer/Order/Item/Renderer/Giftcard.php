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
 * Customer order Giftcard xml renderer
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Block_Customer_Order_Item_Renderer_Giftcard
    extends Enterprise_GiftCard_Block_Sales_Order_Item_Renderer
{
    /**
     * Prepare custom option for display, returns false if there's no value
     *
     * @param string $code
     * @return mixed
     */
    protected function _prepareCustomOption($code)
    {
        if ($option = $this->getOrderItem()->getProductOptionByCode($code)) {
            return strip_tags($option);
        }
        return false;
    }

    /**
     * Prepare a string containing name and email
     *
     * @param string $name
     * @param string $email
     * @return mixed
     */
    protected function _getNameEmailString($name, $email)
    {
        return $name . ' (' . $email . ')';
    }

    /**
     * Add item to XML object
     * (get from template: Mage_Sales::order/items/renderer/default.phtml)
     *
     * @param Mage_XmlConnect_Model_Simplexml_Element $orderItemXmlObj
     * @return null
     */
    public function addItemToXmlObject(Mage_XmlConnect_Model_Simplexml_Element $orderItemXmlObj)
    {
        $item = $this->getOrderItem();
        $item->setProductOptions(array('additional_options' => $this->getItemOptions()));

        $defaultRenderer = $this->getLayout()->getBlock('xmlconnect.customer.order.items')->getItemRenderer(null);
        $defaultRenderer->setItem($item);
        $defaultRenderer->addItemToXmlObject($orderItemXmlObj);
    }
}
