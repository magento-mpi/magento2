<?php
/**
 * Gift card API
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GiftCard\Model\Customer;

class Api extends \Magento\Api\Model\Resource\AbstractResource
{
    /**
     * Retrieve GiftCard data
     *
     * @param string $code
     * @return array
     */
    public function info($code)
    {
        /** @var $card \Magento\GiftCardAccount\Model\Giftcardaccount */
        $card = $this->_getGiftCard($code);

        try {
            $card->isValid(true, true, false, false);
        } catch (\Magento\Core\Exception $e) {
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
        if (!\Mage::helper('Magento\CustomerBalance\Helper\Data')->isEnabled()) {
            $this->_fault('redemption_disabled');
        }
        /** @var $card \Magento\GiftCardAccount\Model\Giftcardaccount */
        $card = $this->_getGiftCard($code);

        \Mage::app()->setCurrentStore(
            \Mage::app()->getStore($store)
        );

        try {
            $card->setIsRedeemed(true)
                    ->redeem($customerId);
        } catch (\Exception $e) {
            $this->_fault('unable_redeem', $e->getMessage());
        }
        return true;
    }

    /**
     * Load gift card by code
     *
     * @param string $code
     * @return \Magento\GiftCardAccount\Model\Giftcardaccount
     */
    protected function _getGiftCard($code)
    {
        $card = \Mage::getModel('\Magento\GiftCardAccount\Model\Giftcardaccount')
            ->loadByCode($code);
        if (!$card->getId()) {
            $this->_fault('not_exists');
        }
        return $card;
    }

}
