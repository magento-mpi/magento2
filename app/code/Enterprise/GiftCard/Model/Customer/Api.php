<?php
/**
 * Gift card API
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Enterprise_GiftCard_Model_Customer_Api extends Magento_Api_Model_Resource_Abstract
{
    /**
     * Retrieve GiftCard data
     *
     * @param string $code
     * @return array
     */
    public function info($code)
    {
        /** @var $card Enterprise_GiftCardAccount_Model_Giftcardaccount */
        $card = $this->_getGiftCard($code);

        try {
            $card->isValid(true, true, false, false);
        } catch (Magento_Core_Exception $e) {
            $this->_fault('not_valid');
        }

        return array(
            'balance' => $card->getBalance(),
            'expire_date' => $card->getDateExpires()
        );
    }

    /**
     * Redeem gift card balance to customer store credit
     *
     * @param string $code
     * @param int $customerId
     * @param int $store
     * @return boolean
     */
    public function redeem($code, $customerId, $store = null)
    {
        if (!Mage::helper('Enterprise_CustomerBalance_Helper_Data')->isEnabled()) {
            $this->_fault('redemption_disabled');
        }
        /** @var $card Enterprise_GiftCardAccount_Model_Giftcardaccount */
        $card = $this->_getGiftCard($code);

        Mage::app()->setCurrentStore(
            Mage::app()->getStore($store)
        );

        try {
            $card->setIsRedeemed(true)
                    ->redeem($customerId);
        } catch (Exception $e) {
            $this->_fault('unable_redeem', $e->getMessage());
        }
        return true;
    }

    /**
     * Load gift card by code
     *
     * @param string $code
     * @return Enterprise_GiftCardAccount_Model_Giftcardaccount
     */
    protected function _getGiftCard($code)
    {
        $card = Mage::getModel('Enterprise_GiftCardAccount_Model_Giftcardaccount')
            ->loadByCode($code);
        if (!$card->getId()) {
            $this->_fault('not_exists');
        }
        return $card;
    }

}
