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
 * @method Magento_GiftCardAccount_Model_Resource_Giftcardaccount _getResource()
 * @method Magento_GiftCardAccount_Model_Resource_Giftcardaccount getResource()
 * @method string getCode()
 * @method Magento_GiftCardAccount_Model_Giftcardaccount setCode(string $value)
 * @method int getStatus()
 * @method Magento_GiftCardAccount_Model_Giftcardaccount setStatus(int $value)
 * @method string getDateCreated()
 * @method Magento_GiftCardAccount_Model_Giftcardaccount setDateCreated(string $value)
 * @method string getDateExpires()
 * @method Magento_GiftCardAccount_Model_Giftcardaccount setDateExpires(string $value)
 * @method int getWebsiteId()
 * @method Magento_GiftCardAccount_Model_Giftcardaccount setWebsiteId(int $value)
 * @method float getBalance()
 * @method Magento_GiftCardAccount_Model_Giftcardaccount setBalance(float $value)
 * @method int getState()
 * @method Magento_GiftCardAccount_Model_Giftcardaccount setState(int $value)
 * @method int getIsRedeemable()
 * @method Magento_GiftCardAccount_Model_Giftcardaccount setIsRedeemable(int $value)
 *
 * @category    Magento
 * @package     Magento_GiftCardAccount
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_GiftCardAccount_Model_Giftcardaccount extends Magento_Core_Model_Abstract
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

    /**
     * Static variable to contain codes, that were saved on previous steps in series of consecutive saves
     * Used if you use different read and write connections
     *
     * @var array
     */
    protected static $_alreadySelectedIds = array();

    /**
     * Gift card account data
     *
     * @var Magento_GiftCardAccount_Helper_Data
     */
    protected $_giftCardAccountData = null;

    /**
     * Core store config
     *
     * @var Magento_Core_Model_Store_Config
     */
    protected $_coreStoreConfig;

    /**
     * Core date
     *
     * @var Magento_Core_Model_Date
     */
    protected $_coreDate = null;

    /**
     * Customer balance balance
     *
     * @var Magento_CustomerBalance_Model_Balance
     */
    protected $_customerBalance = null;

    /**
     * Core email template
     *
     * @var Magento_Core_Model_Email_Template
     */
    protected $_coreEmailTemplate = null;

    /**
     * Locale
     *
     * @var Magento_Core_Model_LocaleInterface
     */
    protected $_locale = null;

    /**
     * Store Manager
     *
     * @var Magento_Core_Model_StoreManagerInterface
     */
    protected $_storeManager = null;

    /**
     * Chrckout Session
     *
     * @var Magento_Checkout_Model_Session
     */
    protected $_checkoutSession = null;

    /**
     * Chrckout Session
     *
     * @var Magento_Customer_Model_Session
     */
    protected $_customerSession = null;

    /**
     * Chrckout Session
     *
     * @var Magento_GiftCardAccount_Model_PoolFactory
     */
    protected $_poolFactory = null;

    /**
     * @param Magento_GiftCardAccount_Helper_Data $giftCardAccountData
     * @param Magento_Core_Model_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param Magento_Core_Model_Store_Config $coreStoreConfig
     * @param Magento_GiftCardAccount_Model_Resource_Giftcardaccount $resource
     * @param Magento_Core_Model_Email_Template $coreEmailTemplate
     * @param Magento_CustomerBalance_Model_Balance $customerBalance
     * @param Magento_Core_Model_Date $coreDate
     * @param Magento_Core_Model_LocaleInterface $locale
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param Magento_Checkout_Model_Session $checkoutSession
     * @param Magento_Customer_Model_Session $customerSession
     * @param Magento_GiftCardAccount_Model_PoolFactory $poolFactory
     * @param Magento_Data_Collection_Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        Magento_GiftCardAccount_Helper_Data $giftCardAccountData,
        Magento_Core_Model_Context $context,
        Magento_Core_Model_Registry $registry,
        Magento_Core_Model_Store_Config $coreStoreConfig,
        Magento_GiftCardAccount_Model_Resource_Giftcardaccount $resource,
        Magento_Core_Model_Email_Template $coreEmailTemplate,
        Magento_CustomerBalance_Model_Balance $customerBalance,
        Magento_Core_Model_Date $coreDate,
        Magento_Core_Model_LocaleInterface $locale,
        Magento_Core_Model_StoreManagerInterface $storeManager,
        Magento_Checkout_Model_Session $checkoutSession,
        Magento_Customer_Model_Session $customerSession,
        Magento_GiftCardAccount_Model_PoolFactory $poolFactory,
        Magento_Data_Collection_Db $resourceCollection = null,
        array $data = array()
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->_giftCardAccountData = $giftCardAccountData;
        $this->_coreStoreConfig = $coreStoreConfig;
        $this->_coreEmailTemplate = $coreEmailTemplate;
        $this->_customerBalance = $customerBalance;
        $this->_coreDate = $coreDate;
        $this->_storeManager = $storeManager;
        $this->_checkoutSession = $checkoutSession;
        $this->_customerSession = $customerSession;
        $this->_locale = $locale;
        $this->_poolFactory = $poolFactory;
    }

    protected function _construct()
    {
        $this->_init('Magento_GiftCardAccount_Model_Resource_Giftcardaccount');
    }

    /**
     * Processing object before save data
     *
     * @return Magento_Core_Model_Abstract|void
     * @throws Magento_Core_Exception
     */
    protected function _beforeSave()
    {
        parent::_beforeSave();

        if (!$this->getId()) {
            $now = $this->_locale->date()
                ->setTimezone(Magento_Core_Model_LocaleInterface::DEFAULT_TIMEZONE)
                ->toString(Magento_Date::DATE_INTERNAL_FORMAT);

            $this->setDateCreated($now);
            if (!$this->hasCode()) {
                $this->_defineCode();
            }
            $this->setIsNew(true);
        } else {
            if ($this->getOrigData('balance') != $this->getBalance()) {
                if ($this->getBalance() > 0) {
                    $this->setState(self::STATE_AVAILABLE);
                } elseif ($this->getIsRedeemable() && $this->getIsRedeemed()) {
                    $this->setState(self::STATE_REDEEMED);
                } else {
                    $this->setState(self::STATE_USED);
                }
            }
        }

        if (is_numeric($this->getLifetime()) && $this->getLifetime() > 0) {
            $this->setDateExpires(date('Y-m-d', strtotime("now +{$this->getLifetime()}days")));
        } else {
            if ($this->getDateExpires()) {
                $expirationDate =  $this->_locale->date(
                    $this->getDateExpires(), Magento_Date::DATE_INTERNAL_FORMAT,
                    null, false);
                $currentDate = $this->_locale->date(
                    null, Magento_Date::DATE_INTERNAL_FORMAT,
                    null, false);
                if ($expirationDate < $currentDate) {
                    throw new Magento_Core_Exception(__('An expiration date must be in the future.'));
                }
            } else {
                $this->setDateExpires(null);
            }
        }

        if (!$this->getId() && !$this->hasHistoryAction()) {
            $this->setHistoryAction(Magento_GiftCardAccount_Model_History::ACTION_CREATED);
        }

        if (!$this->hasHistoryAction() && $this->getOrigData('balance') != $this->getBalance()) {
            $this->setHistoryAction(Magento_GiftCardAccount_Model_History::ACTION_UPDATED)
                ->setBalanceDelta($this->getBalance() - $this->getOrigData('balance'));
        }
        if ($this->getBalance() < 0) {
            throw new Magento_Core_Exception(__('The balance cannot be less than zero.'));
        }
    }

    protected function _afterSave()
    {
        if ($this->getIsNew()) {
            $this->getPoolModel()
                ->setId($this->getCode())
                ->setStatus(Magento_GiftCardAccount_Model_Pool_Abstract::STATUS_USED)
                ->save();
            self::$_alreadySelectedIds[] = $this->getCode();
        }

        parent::_afterSave();
    }

    /**
     * Generate and save gift card account code
     *
     * @return Magento_GiftCardAccount_Model_Giftcardaccount
     */
    protected function _defineCode()
    {
        return $this->setCode($this->getPoolModel()->setExcludedIds(self::$_alreadySelectedIds)->shift());
    }


    /**
     * Load gift card account model using specified code
     *
     * @param string $code
     * @return Magento_GiftCardAccount_Model_Giftcardaccount
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
     * @param Magento_Sales_Model_Quote|null $quote
     * @return Magento_GiftCardAccount_Model_Giftcardaccount
     * @throws Magento_Core_Exception
     */
    public function addToCart($saveQuote = true, $quote = null)
    {
        if (is_null($quote)) {
            $quote = $this->_checkoutSession->getQuote();
        }
        $website = $this->_storeManager->getStore($quote->getStoreId())->getWebsite();
        if ($this->isValid(true, true, $website)) {
            $cards = $this->_giftCardAccountData->getCards($quote);
            if (!$cards) {
                $cards = array();
            } else {
                foreach ($cards as $one) {
                    if ($one['i'] == $this->getId()) {
                        throw new Magento_Core_Exception(__('This gift card account is already in the quote.'));
                    }
                }
            }
            $cards[] = array(
                'i'=>$this->getId(),        // id
                'c'=>$this->getCode(),      // code
                'a'=>$this->getBalance(),   // amount
                'ba'=>$this->getBalance(),  // base amount
            );
            $this->_giftCardAccountData->setCards($quote, $cards);

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
     * @param Magento_Sales_Model_Quote|null $quote
     * @return Magento_GiftCardAccount_Model_Giftcardaccount
     */
    public function removeFromCart($saveQuote = true, $quote = null)
    {
        if (!$this->getId()) {
            $this->_throwException(__('Please correct the gift card account code: "%1".', $this->_requestedCode));
        }
        if (is_null($quote)) {
            $quote = $this->_checkoutSession->getQuote();
        }

        $cards = $this->_giftCardAccountData->getCards($quote);
        if ($cards) {
            foreach ($cards as $k => $one) {
                if ($one['i'] == $this->getId()) {
                    unset($cards[$k]);
                    $this->_giftCardAccountData->setCards($quote, $cards);

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
     * Check if this gift card is expired at the moment
     *
     * @return bool
     */
    public function isExpired()
    {
        if (!$this->getDateExpires()) {
            return false;
        }

        $currentDate = strtotime($this->_coreDate->date('Y-m-d'));

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
            $website = $this->_storeManager->getWebsite($websiteCheck)->getId();
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
                ->setHistoryAction(Magento_GiftCardAccount_Model_History::ACTION_USED);
        }

        return $this;
    }

    /**
     * Revert amount to gift card balance if order was not placed
     *
     * @param   float $amount
     * @return  Magento_GiftCardAccount_Model_Giftcardaccount
     */
    public function revert($amount)
    {
        $amount = (float) $amount;

        if ($amount > 0 && $this->isValid(true, true, false, false)) {
            $this->setBalanceDelta($amount)
                ->setBalance($this->getBalance() + $amount)
                ->setHistoryAction(Magento_GiftCardAccount_Model_History::ACTION_UPDATED);
        }

        return $this;
    }

    /**
     * Set state text on after load
     *
     * @return Magento_GiftCardAccount_Model_Giftcardaccount
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
     * Retreive pool model instance
     *
     * @return Magento_GiftCardAccount_Model_Pool_Abstract
     */
    public function getPoolModel()
    {
        return $this->_poolFactory->create();
    }

    /**
     * Update gift card accounts state
     *
     * @param array $ids
     * @param int $state
     * @return Magento_GiftCardAccount_Model_Giftcardaccount
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
     * @param int $customerId
     * @return Magento_GiftCardAccount_Model_Giftcardaccount
     * @throws Magento_Core_Exception
     */
    public function redeem($customerId = null)
    {
        if ($this->isValid(true, true, true, true)) {
            if ($this->getIsRedeemable() != self::REDEEMABLE) {
                $this->_throwException(sprintf('Gift card account %s is not redeemable.', $this->getId()));
            }
            if (is_null($customerId)) {
                $customerId = $this->_customerSession->getCustomerId();
            }
            if (!$customerId) {
                throw new Magento_Core_Exception(__('You supplied an invalid customer ID.'));
            }

            $additionalInfo = __('Gift Card Redeemed: %1. For customer #%2.', $this->getCode(), $customerId);

            $balance = $this->_customerBalance
                ->setCustomerId($customerId)
                ->setWebsiteId($this->_storeManager->getWebsite()->getId())
                ->setAmountDelta($this->getBalance())
                ->setNotifyByEmail(false)
                ->setUpdatedActionAdditionalInfo($additionalInfo)
                ->save();

            $this->setBalanceDelta(-$this->getBalance())
                ->setHistoryAction(Magento_GiftCardAccount_Model_History::ACTION_REDEEMED)
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
            $recipientStore = $this->_storeManager->getWebsite($this->getWebsiteId())->getDefaultStore();
        } else {
            $recipientStore = $this->_storeManager->getStore($recipientStore);
        }

        $storeId = $recipientStore->getId();

        $balance = $this->getBalance();
        $code = $this->getCode();

        $balance = $this->_locale->currency($recipientStore->getBaseCurrencyCode())->toCurrency($balance);

        $email = $this->_coreEmailTemplate->setDesignConfig(array('store' => $storeId));
        $email->sendTransactional(
            $this->_coreStoreConfig->getConfig('giftcard/giftcardaccount_email/template', $storeId),
            $this->_coreStoreConfig->getConfig('giftcard/giftcardaccount_email/identity', $storeId),
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
                ->setHistoryAction(Magento_GiftCardAccount_Model_History::ACTION_SENT)
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
     * @throws Magento_Core_Exception
     * @param string $realMessage
     * @param string $fakeMessage
     */
    protected function _throwException($realMessage, $fakeMessage = '')
    {
        $e = new Magento_Core_Exception($realMessage);
        $this->_logger->logException($e);
        if (!$fakeMessage) {
            $fakeMessage = __('Please correct the gift card code.');
        }
        $e->setMessage((string)$fakeMessage);
        throw $e;
    }
}
