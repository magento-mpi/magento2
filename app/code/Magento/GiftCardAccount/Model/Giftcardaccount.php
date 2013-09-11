<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftCardAccount
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @method \Magento\GiftCardAccount\Model\Resource\Giftcardaccount _getResource()
 * @method \Magento\GiftCardAccount\Model\Resource\Giftcardaccount getResource()
 * @method string getCode()
 * @method \Magento\GiftCardAccount\Model\Giftcardaccount setCode(string $value)
 * @method int getStatus()
 * @method \Magento\GiftCardAccount\Model\Giftcardaccount setStatus(int $value)
 * @method string getDateCreated()
 * @method \Magento\GiftCardAccount\Model\Giftcardaccount setDateCreated(string $value)
 * @method string getDateExpires()
 * @method \Magento\GiftCardAccount\Model\Giftcardaccount setDateExpires(string $value)
 * @method int getWebsiteId()
 * @method \Magento\GiftCardAccount\Model\Giftcardaccount setWebsiteId(int $value)
 * @method float getBalance()
 * @method \Magento\GiftCardAccount\Model\Giftcardaccount setBalance(float $value)
 * @method int getState()
 * @method \Magento\GiftCardAccount\Model\Giftcardaccount setState(int $value)
 * @method int getIsRedeemable()
 * @method \Magento\GiftCardAccount\Model\Giftcardaccount setIsRedeemable(int $value)
 *
 * @category    Magento
 * @package     Magento_GiftCardAccount
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\GiftCardAccount\Model;

class Giftcardaccount extends \Magento\Core\Model\AbstractModel
{
    const STATUS_DISABLED = 0;
    const STATUS_ENABLED  = 1;

    const STATE_AVAILABLE = 0;
    const STATE_USED      = 1;
    const STATE_REDEEMED  = 2;
    const STATE_EXPIRED   = 3;

    const REDEEMABLE     = 1;
    const NOT_REDEEMABLE = 0;

    protected $_eventPrefix = 'magento_giftcardaccount';
    protected $_eventObject = 'giftcardaccount';
    /**
     * Giftcard code that was requested for load
     *
     * @var bool|string
     */
    protected $_requestedCode = false;

    protected $_defaultPoolModelClass = '\Magento\GiftCardAccount\Model\Pool';

    /**
     * Static variable to contain codes, that were saved on previous steps in series of consecutive saves
     * Used if you use different read and write connections
     *
     * @var array
     */
    protected static $_alreadySelectedIds = array();

    protected function _construct()
    {
        $this->_init('\Magento\GiftCardAccount\Model\Resource\Giftcardaccount');
    }

    protected function _beforeSave()
    {
        parent::_beforeSave();

        if (!$this->getId()) {
            $now = \Mage::app()->getLocale()->date()
                ->setTimezone(\Mage::DEFAULT_TIMEZONE)
                ->toString(\Magento\Date::DATE_INTERNAL_FORMAT);

            $this->setDateCreated($now);
            if (!$this->hasCode()) {
                $this->_defineCode();
            }
            $this->setIsNew(true);
        } else {
            if ($this->getOrigData('balance') != $this->getBalance()) {
                if ($this->getBalance() > 0) {
                    $this->setState(self::STATE_AVAILABLE);
                }
                elseif ($this->getIsRedeemable() && $this->getIsRedeemed())  {
                    $this->setState(self::STATE_REDEEMED);
                }
                else {
                    $this->setState(self::STATE_USED);
                }
            }
        }

        if (is_numeric($this->getLifetime()) && $this->getLifetime() > 0) {
            $this->setDateExpires(date('Y-m-d', strtotime("now +{$this->getLifetime()}days")));
        } else {
            if ($this->getDateExpires()) {
                $expirationDate =  \Mage::app()->getLocale()->date(
                    $this->getDateExpires(), \Magento\Date::DATE_INTERNAL_FORMAT,
                    null, false);
                $currentDate = \Mage::app()->getLocale()->date(
                    null, \Magento\Date::DATE_INTERNAL_FORMAT,
                    null, false);
                if ($expirationDate < $currentDate) {
                    \Mage::throwException(__('An expiration date must be in the future.'));
                }
            } else {
                $this->setDateExpires(null);
            }
        }

        if (!$this->getId() && !$this->hasHistoryAction()) {
            $this->setHistoryAction(\Magento\GiftCardAccount\Model\History::ACTION_CREATED);
        }

        if (!$this->hasHistoryAction() && $this->getOrigData('balance') != $this->getBalance()) {
            $this->setHistoryAction(\Magento\GiftCardAccount\Model\History::ACTION_UPDATED)
                ->setBalanceDelta($this->getBalance() - $this->getOrigData('balance'));
        }
        if ($this->getBalance() < 0) {
            \Mage::throwException(__('The balance cannot be less than zero.'));
        }
    }

    protected function _afterSave()
    {
        if ($this->getIsNew()) {
            $this->getPoolModel()
                ->setId($this->getCode())
                ->setStatus(\Magento\GiftCardAccount\Model\Pool\AbstractPool::STATUS_USED)
                ->save();
            self::$_alreadySelectedIds[] = $this->getCode();
        }

        parent::_afterSave();
    }

    /**
     * Generate and save gift card account code
     *
     * @return \Magento\GiftCardAccount\Model\Giftcardaccount
     */
    protected function _defineCode()
    {
        return $this->setCode($this->getPoolModel()->setExcludedIds(self::$_alreadySelectedIds)->shift());
    }


    /**
     * Load gift card account model using specified code
     *
     * @param string $code
     * @return \Magento\GiftCardAccount\Model\Giftcardaccount
     */
    public function loadByCode($code)
    {
        $this->_requestedCode = $code;

        return $this->load($code, 'code');
    }


    /**
     * Add gift card to quote gift card storage
     *
     * @param bool $saveQuote
     * @return \Magento\GiftCardAccount\Model\Giftcardaccount
     */
    public function addToCart($saveQuote = true, $quote = null)
    {
        if (is_null($quote)) {
            $quote = $this->_getCheckoutSession()->getQuote();
        }
        $website = \Mage::app()->getStore($quote->getStoreId())->getWebsite();
        if ($this->isValid(true, true, $website)) {
            $cards = \Mage::helper('Magento\GiftCardAccount\Helper\Data')->getCards($quote);
            if (!$cards) {
                $cards = array();
            } else {
                foreach ($cards as $one) {
                    if ($one['i'] == $this->getId()) {
                        \Mage::throwException(__('This gift card account is already in the quote.'));
                    }
                }
            }
            $cards[] = array(
                'i'=>$this->getId(),        // id
                'c'=>$this->getCode(),      // code
                'a'=>$this->getBalance(),   // amount
                'ba'=>$this->getBalance(),  // base amount
            );
            \Mage::helper('Magento\GiftCardAccount\Helper\Data')->setCards($quote, $cards);

            if ($saveQuote) {
                $quote->collectTotals()->save();
            }
        }

        return $this;
    }

    /**
     * Remove gift card from quote gift card storage
     *
     * @param bool $saveQuote
     * @param \Magento\Sales\Model\Quote|null $quote
     * @return \Magento\GiftCardAccount\Model\Giftcardaccount
     */
    public function removeFromCart($saveQuote = true, $quote = null)
    {
        if (!$this->getId()) {
            $this->_throwException(__('Please correct the gift card account code: "%1".', $this->_requestedCode));
        }
        if (is_null($quote)) {
            $quote = $this->_getCheckoutSession()->getQuote();
        }

        $cards = \Mage::helper('Magento\GiftCardAccount\Helper\Data')->getCards($quote);
        if ($cards) {
            foreach ($cards as $k => $one) {
                if ($one['i'] == $this->getId()) {
                    unset($cards[$k]);
                    \Mage::helper('Magento\GiftCardAccount\Helper\Data')->setCards($quote, $cards);

                    if ($saveQuote) {
                        $quote->collectTotals()->save();
                    }
                    return $this;
                }
            }
        }

        $this->_throwException(__('This gift card account wasn\'t found in the quote.'));
    }

    /**
     * Return checkout/session model singleton
     *
     * @return \Magento\Checkout\Model\Session
     */
    protected function _getCheckoutSession()
    {
        return \Mage::getSingleton('Magento\Checkout\Model\Session');
    }

    /**
     * Check if this gift card is expired at the moment
     *
     * @return bool
     */
    public function isExpired()
    {
        if (!$this->getDateExpires()) {
            return false;
        }

        $currentDate = strtotime(\Mage::getModel('\Magento\Core\Model\Date')->date('Y-m-d'));

        if (strtotime($this->getDateExpires()) < $currentDate) {
            return true;
        }
        return false;
    }


    /**
     * Check all the gift card validity attributes
     *
     * @param bool $expirationCheck
     * @param bool $statusCheck
     * @param mixed $websiteCheck
     * @param mixed $balanceCheck
     * @return bool
     */
    public function isValid($expirationCheck = true, $statusCheck = true, $websiteCheck = false, $balanceCheck = true)
    {
        if (!$this->getId()) {
            $this->_throwException(
                __('Please correct the gift card account ID. Requested code: "%1"', $this->_requestedCode)
            );
        }

        if ($websiteCheck) {
            if ($websiteCheck === true) {
                $websiteCheck = null;
            }
            $website = \Mage::app()->getWebsite($websiteCheck)->getId();
            if ($this->getWebsiteId() != $website) {
                $this->_throwException(
                    __('Please correct the gift card account website: %1.', $this->getWebsiteId())
                );
            }
        }

        if ($statusCheck && ($this->getStatus() != self::STATUS_ENABLED)) {
            $this->_throwException(
                __('Gift card account %1 is not enabled.', $this->getId())
            );
        }

        if ($expirationCheck && $this->isExpired()) {
            $this->_throwException(
                __('Gift card account %1 is expired.', $this->getId())
            );
        }

        if ($balanceCheck) {
            if ($this->getBalance() <= 0) {
                $this->_throwException(
                    __('Gift card account %1 has a zero balance.', $this->getId())
                );
            }
            if ($balanceCheck !== true && is_numeric($balanceCheck)) {
                if ($this->getBalance() < $balanceCheck) {
                    $this->_throwException(
                        __('Gift card account %1 balance is lower than the charged amount.', $this->getId())
                    );
                }
            }
        }

        return true;
    }

    /**
     * Reduce Gift Card Account balance by specified amount
     *
     * @param decimal $amount
     */
    public function charge($amount)
    {
        if ($this->isValid(false, false, false, $amount)) {
            $this->setBalanceDelta(-$amount)
                ->setBalance($this->getBalance() - $amount)
                ->setHistoryAction(\Magento\GiftCardAccount\Model\History::ACTION_USED);
        }

        return $this;
    }

    /**
     * Revert amount to gift card balance if order was not placed
     *
     * @param   float $amount
     * @return  \Magento\GiftCardAccount\Model\Giftcardaccount
     */
    public function revert($amount)
    {
        $amount = (float) $amount;

        if ($amount > 0 && $this->isValid(true, true, false, false)) {
            $this->setBalanceDelta($amount)
                ->setBalance($this->getBalance() + $amount)
                ->setHistoryAction(\Magento\GiftCardAccount\Model\History::ACTION_UPDATED);
        }

        return $this;
    }

    /**
     * Set state text on after load
     *
     * @return \Magento\GiftCardAccount\Model\Giftcardaccount
     */
    public function _afterLoad()
    {
        $this->_setStateText();
        return parent::_afterLoad();
    }

    /**
     * Return Gift Card Account state options
     *
     * @return array
     */
    public function getStatesAsOptionList()
    {
        $result = array();

        $result[self::STATE_AVAILABLE] = __('Available');
        $result[self::STATE_USED]      = __('Used');
        $result[self::STATE_REDEEMED]  = __('Redeemed');
        $result[self::STATE_EXPIRED]   = __('Expired');

        return $result;
    }

    /**
     * Return code pool model class name
     *
     * @return string
     */
    public function getPoolModelClass()
    {
        if (!$this->hasPoolModelClass()) {
            $this->setPoolModelClass($this->_defaultPoolModelClass);
        }
        return $this->getData('pool_model_class');
    }

    /**
     * Retreive pool model instance
     *
     * @return \Magento\GiftCardAccount\Model\Pool\AbstractPool
     */
    public function getPoolModel()
    {
        return \Mage::getModel($this->getPoolModelClass());
    }

    /**
     * Update gift card accounts state
     *
     * @param array $ids
     * @param int $state
     * @return \Magento\GiftCardAccount\Model\Giftcardaccount
     */
    public function updateState($ids, $state)
    {
        if ($ids) {
            $this->getResource()->updateState($ids, $state);
        }
        return $this;
    }

    /**
     * Redeem gift card (-gca balance, +cb balance)
     *
     * @return \Magento\GiftCardAccount\Model\Giftcardaccount
     */
    public function redeem($customerId = null)
    {
        if ($this->isValid(true, true, true, true)) {
            if ($this->getIsRedeemable() != self::REDEEMABLE) {
                $this->_throwException(sprintf('Gift card account %s is not redeemable.', $this->getId()));
            }
            if (is_null($customerId)) {
                $customerId = \Mage::getSingleton('Magento\Customer\Model\Session')->getCustomerId();
            }
            if (!$customerId) {
                \Mage::throwException(__('You supplied an invalid customer ID.'));
            }

            $additionalInfo = __('Gift Card Redeemed: %1. For customer #%2.', $this->getCode(), $customerId);

            $balance = \Mage::getModel('\Magento\CustomerBalance\Model\Balance')
                ->setCustomerId($customerId)
                ->setWebsiteId(\Mage::app()->getWebsite()->getId())
                ->setAmountDelta($this->getBalance())
                ->setNotifyByEmail(false)
                ->setUpdatedActionAdditionalInfo($additionalInfo)
                ->save();

            $this->setBalanceDelta(-$this->getBalance())
                ->setHistoryAction(\Magento\GiftCardAccount\Model\History::ACTION_REDEEMED)
                ->setBalance(0)
                ->setCustomerId($customerId)
                ->save();
        }

        return $this;
    }

    public function sendEmail()
    {
        $recipientName = $this->getRecipientName();
        $recipientEmail = $this->getRecipientEmail();
        $recipientStore = $this->getRecipientStore();
        if (is_null($recipientStore)) {
            $recipientStore = \Mage::app()->getWebsite($this->getWebsiteId())->getDefaultStore();
        } else {
            $recipientStore = \Mage::app()->getStore($recipientStore);
        }

        $storeId = $recipientStore->getId();

        $balance = $this->getBalance();
        $code = $this->getCode();

        $balance = \Mage::app()->getLocale()->currency($recipientStore->getBaseCurrencyCode())->toCurrency($balance);

        $email = \Mage::getModel('\Magento\Core\Model\Email\Template')->setDesignConfig(array('store' => $storeId));
        $email->sendTransactional(
            \Mage::getStoreConfig('giftcard/giftcardaccount_email/template', $storeId),
            \Mage::getStoreConfig('giftcard/giftcardaccount_email/identity', $storeId),
            $recipientEmail,
            $recipientName,
            array(
                'name'          => $recipientName,
                'code'          => $code,
                'balance'       => $balance,
                'store'         => $recipientStore,
                'store_name'    => $recipientStore->getName(),
            )
        );

        $this->setEmailSent(false);
        if ($email->getSentSuccess()) {
            $this->setEmailSent(true)
                ->setHistoryAction(\Magento\GiftCardAccount\Model\History::ACTION_SENT)
                ->save();
        }
    }

    /**
     * Set state text by loaded state code
     * Used in _afterLoad()
     *
     * @return string
     */
    protected function _setStateText()
    {
        $states = $this->getStatesAsOptionList();

        if (isset($states[$this->getState()])) {
            $stateText = $states[$this->getState()];
            $this->setStateText($stateText);
            return $stateText;
        }
        return '';
    }

    /**
     * Obscure real exception message to prevent brute force attacks
     *
     * @throws \Magento\Core\Exception
     * @param string $realMessage
     * @param string $fakeMessage
     */
    protected function _throwException($realMessage, $fakeMessage = '')
    {
        $e = \Mage::exception('Magento_Core', $realMessage);
        \Mage::logException($e);
        if (!$fakeMessage) {
            $fakeMessage = __('Please correct the gift card code.');
        }
        $e->setMessage((string)$fakeMessage);
        throw $e;
    }
}
