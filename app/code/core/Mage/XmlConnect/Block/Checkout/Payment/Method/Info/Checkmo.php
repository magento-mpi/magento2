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
 * Check / Money order Payment info xml renderer
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Block_Checkout_Payment_Method_Info_Checkmo extends Mage_Payment_Block_Info_Checkmo
{
    /**
     * Add Check / Money order info to order XML object
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

        if ($this->getInfo()->getAdditionalData()) {
            if ($this->getPayableTo()) {
                $orderItemXmlObj->addCustomChild('item', $this->getPayableTo(), array(
                    'label' => Mage::helper('Mage_Sales_Helper_Data')->__('Make Check payable to:')
                ));
            }
            if ($this->getMailingAddress()) {
                $orderItemXmlObj->addCustomChild('item', $this->getMailingAddress(), array(
                    'label' => Mage::helper('Mage_Payment_Helper_Data')->__('Send Check to:')
                ));
            }
        }
    }
}
