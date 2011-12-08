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
 * Purchase Order Payment info xml renderer
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Block_Checkout_Payment_Method_Info_Purchaseorder extends Mage_Payment_Block_Info_Purchaseorder
{
    /**
     * Add Purchase Order Payment info to order XML object
     *
     * @param Mage_XmlConnect_Model_Simplexml_Element $orderItemXmlObj
     * @return Mage_XmlConnect_Model_Simplexml_Element
     */
    public function addPaymentInfoToXmlObj(Mage_XmlConnect_Model_Simplexml_Element $orderItemXmlObj)
    {
        $orderItemXmlObj->addAttribute('type', $this->getMethod()->getCode());
        $orderItemXmlObj->addAttribute('title', $orderItemXmlObj->xmlAttribute($this->getMethod()->getTitle()));

        $orderItemXmlObj->addCustomChild('item', $this->getInfo()->getPoNumber(), array(
            'label' => Mage::helper('Mage_Sales_Helper_Data')->__('Purchase Order Number:')
        ));
    }
}
