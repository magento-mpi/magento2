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
 * One page checkout billing addresses xml renderer
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Block_Checkout_Address_Billing extends Mage_Checkout_Block_Onepage_Billing
{
    /**
     * Render billing addresses xml
     *
     * @return string
     */
    protected function _toHtml()
    {
        $billingXmlObj = Mage::getModel('Mage_XmlConnect_Model_Simplexml_Element',
            array('data' => '<billing></billing>'));

        $addressId = $this->getAddress()->getId();
        $address = $this->getCustomer()->getPrimaryBillingAddress();
        if ($address) {
            $addressId = $address->getId();
        }

        foreach ($this->getCustomer()->getAddresses() as $address) {
            $item = $billingXmlObj->addChild('item');
            if ($addressId == $address->getId()) {
                $item->addAttribute('selected', 1);
            }
            $this->getChildBlock('address_list')->prepareAddressData($address, $item);
            $item->addChild(
                'address_line', $billingXmlObj->escapeXml($address->format('oneline'))
            );
        }

        return $billingXmlObj->asNiceXml();
    }
}
