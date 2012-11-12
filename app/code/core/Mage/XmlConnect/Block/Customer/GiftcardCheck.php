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
 * Check Gift card xml renderer
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Block_Customer_GiftcardCheck extends Mage_Core_Block_Template
{
    /**
     * Render gift card info xml
     *
     * @return string
     */
    protected function _toHtml()
    {
        /** @var $card Enterprise_GiftCardAccount_Model_Giftcardaccount */
        $card = Mage::registry('current_giftcardaccount');
        if ($card && $card->getId()) {
            /** @var $xmlModel Mage_XmlConnect_Model_Simplexml_Element */
            $xmlModel = Mage::getModel('Mage_XmlConnect_Model_Simplexml_Element',
                array('data' => '<gift_card_account></gift_card_account>'));

            $balance = Mage::helper('Mage_Core_Helper_Data')->currency($card->getBalance(), true, false);

            $result[] = $this->__("Gift Card: %s", $card->getCode());
            $result[] = $this->__('Current Balance: %s', $balance);

            if ($card->getDateExpires()) {
                $result[] = $this->__('Expires: %s', $this->formatDate($card->getDateExpires(), 'short'));
            }
            $xmlModel->addCustomChild('info', implode(PHP_EOL, $result));
        } else {
            $xmlModel = Mage::getModel('Mage_XmlConnect_Model_Simplexml_Element',
                array('data' => '<message></message>'));
            $xmlModel->addCustomChild('status', Mage_XmlConnect_Controller_Action::MESSAGE_STATUS_ERROR);
            $xmlModel->addCustomChild('text', $this->__('Wrong or expired Gift Card Code.'));
        }

        return $xmlModel->asNiceXml();
    }
}
