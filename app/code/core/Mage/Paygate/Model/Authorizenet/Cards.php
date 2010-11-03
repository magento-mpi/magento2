<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Paygate
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Mage_Paygate_Model_Authorizenet_Cards// extends Mage_Core_Model_Abstract
{
    const CARDS_NAMESPACE = 'authorize_cards';

    /**
     * Cards information
     *
     * @var mixed
     */
    protected $_cards = array();

    /**
     * Payment instance
     *
     * @var Mage_Payment_Model_Info
     */
    protected $_payment = null;

    /**
     * Set payment instance for storing credit card information and partial authorizations
     *
     * @param Mage_Payment_Model_Info
     * @return Mage_Paygate_Model_Authorizenet_Cart
     */
    public function setPayment(Mage_Payment_Model_Info $payment)
    {
        $this->_payment = $payment;
        $paymentCardsInformation = $this->_payment->getAdditionalInformation(self::CARDS_NAMESPACE);
        if ($paymentCardsInformation) {
            $this->_cards = $paymentCardsInformation;
        }

        return $this;
    }

    /**
     * Add based on $cardInfo card to payment
     *
     * @param mixed $cardInfo
     * @return Mage_Paygate_Model_Authorizenet_Cart
     */
    public function addCard($cardInfo)
    {
        $this->_isPaymentValid();
        $this->_cards[] = $cardInfo;
        $this->_payment->setAdditionalInformation(self::CARDS_NAMESPACE, $this->_cards);
        return $this;
    }

    /**
     * Get all stored cards
     *
     * @return mixed
     */
    public function getCards()
    {
        $this->_isPaymentValid();
        return $this->_cards;
    }

    /**
     * Return count of saved cards
     *
     * @return int
     */
    public function getCardsCount()
    {
        $this->_isPaymentValid();
        return count($this->_cards);
    }

    /**
     * Return count of saved cards
     *
     * @return int
     */
    public function getProcessedAmount()
    {
        $amount = 0;
        if ($this->getCards()) {
            foreach ($this->getCards() as $card) {
                $amount += $card['processed_amount'];
            }
        }
        return $amount;
    }

    /**
     * Remove all cards from payment instance
     *
     * @return Mage_Paygate_Model_Authorizenet_Cart
     */
    public function flushCards()
    {
        $this->_payment->setAdditionalInformation(self::CARDS_NAMESPACE, null);
        return $this;
    }

    /**
     * Check for payment instace present
     *
     * @throws Exception
     */
    protected function _isPaymentValid()
    {
        if (!$this->_payment) {
            throw new Exception('Payment instance is not set');
        }
    }

}
