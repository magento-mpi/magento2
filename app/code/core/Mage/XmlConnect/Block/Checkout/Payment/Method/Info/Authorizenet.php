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
 * Authorizenet Payment info xml renderer
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Block_Checkout_Payment_Method_Info_Authorizenet extends Mage_Paygate_Block_Authorizenet_Info_Cc
{
    /**
     * Add Authorizenet info to order XML object
     *
     * @param Mage_XmlConnect_Model_Simplexml_Element $orderItemXmlObj
     * @return Mage_XmlConnect_Model_Simplexml_Element
     */
    public function addPaymentInfoToXmlObj(Mage_XmlConnect_Model_Simplexml_Element $orderItemXmlObj)
    {
        $orderItemXmlObj->addAttribute('type', $this->getMethod()->getCode());
        if (!$this->getHideTitle()) {
            $orderItemXmlObj->addAttribute('title', $orderItemXmlObj->xmlAttribute($this->getMethod()->getTitle()));
        }

        $cards = $this->getCards();
        $showCount = count($cards) > 1;

        foreach ($cards as $key => $card) {
            $creditCard = $orderItemXmlObj->addCustomChild('item', null, array(
                'label' => $showCount ? $this->__('Credit Card %s', $key + 1) : $this->__('Credit Card')
            ));
            foreach ($card as $label => $value) {
                $creditCard->addCustomChild('item', implode($this->getValueAsArray($value, true), '\n'), array(
                    'label' => $label
                ));
            }
        }
    }
}
