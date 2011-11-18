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
 * Free Payment info xml renderer
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Block_Checkout_Payment_Method_Info_Free extends Mage_Payment_Block_Info
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
        $orderItemXmlObj->addAttribute('title', $orderItemXmlObj->xmlAttribute($this->getMethod()->getTitle()));
    }
}
