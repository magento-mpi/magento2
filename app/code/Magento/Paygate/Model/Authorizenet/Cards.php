<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Paygate
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Paygate\Model\Authorizenet;

class Cards
{
    const CARDS_NAMESPACE = 'authorize_cards';
    const CARD_ID_KEY = 'id';
    const CARD_PROCESSED_AMOUNT_KEY = 'processed_amount';
    const CARD_CAPTURED_AMOUNT_KEY = 'captured_amount';
    const CARD_REFUNDED_AMOUNT_KEY = 'refunded_amount';

    /**
     * Cards information
     *
     * @var mixed
     */
    protected $_cards = array();

    /**
     * Payment instance
     *
     * @var \Magento\Payment\Model\Info
     */
    protected $_payment = null;

    /**
     * Set payment instance for storing credit card information and partial authorizations
     *
     * @param \Magento\Payment\Model\Info $payment
     * @return \Magento\Paygate\Model\Authorizenet\Cards
     */
    public function setPayment(\Magento\Payment\Model\Info $payment)
    {
        $this->_payment = $payment;
        $paymentCardsInformation = $this->_payment->getAdditionalInformation(self::CARDS_NAMESPACE);
        if ($paymentCardsInformation) {
            $this->_cards = $paymentCardsInformation;
        }

        return $this;
    }

    /**
     * Add based on $cardInfo card to payment and return Id of new item
     *
     * @param mixed $cardInfo
     * @return string
     */
    public function registerCard($cardInfo = array())
    {
        $this->_isPaymentValid();
        $cardId = md5(microtime(1));
        $cardInfo[self::CARD_ID_KEY] = $cardId;
        $this->_cards[$cardId] = $cardInfo;
        $this->_payment->setAdditionalInformation(self::CARDS_NAMESPACE, $this->_cards);
        return $this->getCard($cardId);
    }

    /**
     * Save data from card object in cards storage
     *
     * @param \Magento\Object $card
     * @return \Magento\Paygate\Model\Authorizenet\Cards
     */
    public function updateCard($card)
    {
        $cardId = $card->getData(self::CARD_ID_KEY);
        if ($cardId && isset($this->_cards[$cardId])) {
            $this->_cards[$cardId] = $card->getData();
            $this->_payment->setAdditionalInformation(self::CARDS_NAMESPACE, $this->_cards);
        }
        return $this;
    }

    /**
     * Retrieve card by ID
     *
     * @param string $cardId
     * @return \Magento\Object|bool
     */
    public function getCard($cardId)
    {
        if (isset($this->_cards[$cardId])) {
            $card = new \Magento\Object($this->_cards[$cardId]);
            return $card;
        }
        return false;
    }

    /**
     * Get all stored cards
     *
     * @return array
     */
    public function getCards()
    {
        $this->_isPaymentValid();
        $_cards = array();
        foreach(array_keys($this->_cards) as $key) {
            $_cards[$key] = $this->getCard($key);
        }
        return $_cards;
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
     * Return processed amount for all cards
     *
     * @return float
     */
    public function getProcessedAmount()
    {
        return $this->_getAmount(self::CARD_PROCESSED_AMOUNT_KEY);
    }

    /**
     * Return captured amount for all cards
     *
     * @return float
     */
    public function getCapturedAmount()
    {
        return $this->_getAmount(self::CARD_CAPTURED_AMOUNT_KEY);
    }

    /**
     * Return refunded amount for all cards
     *
     * @return float
     */
    public function getRefundedAmount()
    {
        return $this->_getAmount(self::CARD_REFUNDED_AMOUNT_KEY);
    }

    /**
     * Remove all cards from payment instance
     *
     * @return \Magento\Paygate\Model\Authorizenet\Cards
     */
    public function flushCards()
    {
        $this->_cards = array();
        $this->_payment->setAdditionalInformation(self::CARDS_NAMESPACE, null);
        return $this;
    }

    /**
     * Check for payment instace present
     *
     * @throws \Exception
     */
    protected function _isPaymentValid()
    {
        if (!$this->_payment) {
            throw new \Exception('Payment instance is not set');
        }
    }
    /**
     * Return total for cards data fields
     *
     * $param string $key
     * @return float
     */
    public function _getAmount($key)
    {
        $amount = 0;
        foreach ($this->_cards as $card) {
            if (isset($card[$key])) {
                $amount += $card[$key];
            }
        }
        return $amount;
    }
}
