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
 * One page checkout shipping addresses xml renderer
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Block_Checkout_Address_Shipping extends Mage_Checkout_Block_Onepage_Shipping
{
    /**
     * Render billing shipping xml
     *
     * @return string
     */
    protected function _toHtml()
    {
        $shippingXmlObj = Mage::getModel('Mage_XmlConnect_Model_Simplexml_Element',
            array('data' => '<shipping></shipping>'));

        $addressId = $this->getAddress()->getId();
        $address = $this->getCustomer()->getPrimaryShippingAddress();
        if ($address) {
            $addressId = $address->getId();
        }

        foreach ($this->getCustomer()->getAddresses() as $address) {
            $item = $shippingXmlObj->addChild('item');
            if ($addressId == $address->getId()) {
                $item->addAttribute('selected', 1);
            }
            $this->getChildBlock('address_list')->prepareAddressData($address, $item);
            $item->addChild('address_line', $shippingXmlObj->escapeXml($address->format('oneline')));
        }

        return $shippingXmlObj->asNiceXml();
    }
}
