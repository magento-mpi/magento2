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
 * Abstract PayPal Payment info xml renderer
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
abstract class Mage_XmlConnect_Block_Checkout_Payment_Method_Info_Paypal_Abstract
    extends Mage_Paypal_Block_Payment_Info
{
    /**
     * Add CC Save Payment info to order XML object
     *
     * @param Mage_XmlConnect_Model_Simplexml_Element $orderItemXmlObj
     * @return Mage_XmlConnect_Model_Simplexml_Element
     */
    public function addPaymentInfoToXmlObj(Mage_XmlConnect_Model_Simplexml_Element $orderItemXmlObj)
    {
        $orderItemXmlObj->addAttribute('type', $this->getMethod()->getCode());
        $orderItemXmlObj->addAttribute(
            'title', $orderItemXmlObj->xmlAttribute($this->getMethod()->getTitle())
        );

        if ($_specificInfo = $this->getSpecificInformation()) {
            foreach ($_specificInfo as $label => $value) {
                $orderItemXmlObj->addCustomChild('item', implode($this->getValueAsArray($value, true), '\n'), array(
                    'label' => $label
                ));
            }
        }
    }
}
