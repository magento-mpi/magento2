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
 * Customer order Customer balance totals xml renderer
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Block_Customer_Order_Totals_Giftcards
    extends Enterprise_GiftCardAccount_Block_Sales_Order_Giftcards
{
    /**
     * Add order total rendered to XML object
     *
     * @param $totalsXml Mage_XmlConnect_Model_Simplexml_Element
     * @return null
     */
    public function addToXmlObject(Mage_XmlConnect_Model_Simplexml_Element $totalsXml)
    {
        $cards  = $this->getGiftCards();
        if ($cards) {
            foreach ($cards as $card) {
                $label = Mage::helper('Enterprise_GiftCardAccount_Helper_Data')->__('Gift Card (%s)', $card->getCode());
                $totalsXml->addCustomChild($this->getTotal()->getCode(), '-' . $this->_formatPrice($card->getAmount()),
                    array('label' => $label)
                );
            }
        } else {
            $cardsAmount = $this->getSource()->getGiftCardsAmount();
            if ($cardsAmount > 0) {
                $totalsXml->addCustomChild($this->getTotal()->getCode(), '-' . $this->_formatPrice($cardsAmount),
                    array('label' => Mage::helper('Enterprise_GiftCardAccount_Helper_Data')->__('Gift Card'))
                );
            }
        }
    }

    /**
     * Format price using order currency
     *
     * @param   float $amount
     * @return  string
     */
    protected function _formatPrice($amount)
    {
        return Mage::helper('Mage_XmlConnect_Helper_Customer_Order')->formatPrice($this, $amount);
    }
}
