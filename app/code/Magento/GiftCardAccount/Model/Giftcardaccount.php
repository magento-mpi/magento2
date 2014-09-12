<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GiftCardAccount\Model;

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
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Giftcardaccount extends \Magento\Framework\Model\AbstractModel
{
    const STATUS_DISABLED = 0;

    const STATUS_ENABLED = 1;

    const STATE_AVAILABLE = 0;

    const STATE_USED = 1;

    const STATE_REDEEMED = 2;

    const STATE_EXPIRED = 3;

    const REDEEMABLE = 1;

    const NOT_REDEEMABLE = 0;

    /**
     * @var string
     */
    protected $_eventPrefix = 'magento_giftcardaccount';

    /**
     * @var string
     */
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
     * @var \Magento\GiftCardAccount\Helper\Data
     */
    protected $_giftCardAccountData = null;

    /**
     * Core store config
     *
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * Core date
     *
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $_coreDate = null;

    /**
     * Customer balance balance
     *
     * @var \Magento\CustomerBalance\Model\Balance
     */
    protected $_customerBalance = null;

    /**
     * @var \Magento\Framework\Mail\Template\TransportBuilder
     */
    protected $_transportBuilder;

    /**
     * @var \Magento\Framework\Locale\CurrencyInterface
     */
    protected $_localeCurrency;

    /**
     * Store Manager
     *
     * @var \Magento\Framework\StoreManagerInterface
     */
    protected $_storeManager = null;

    /**
     * Chrckout Session
     *
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession = null;

    /**
     * Chrckout Session
     *
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession = null;

    /**
     * Chrckout Session
     *
     * @var \Magento\GiftCardAccount\Model\PoolFactory
     */
    protected $_poolFactory = null;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $_localeDate;

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\GiftCardAccount\Helper\Data $giftCardAccountData
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\GiftCardAccount\Model\Resource\Giftcardaccount $resource
     * @param \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
     * @param \Magento\CustomerBalance\Model\Balance $customerBalance
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $coreDate
     * @param \Magento\Framework\Locale\CurrencyInterface $localeCurrency
     * @param \Magento\Framework\StoreManagerInterface $storeManager
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\GiftCardAccount\Model\PoolFactory $poolFactory
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
     * @param \Magento\Framework\Data\Collection\Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\GiftCardAccount\Helper\Data $giftCardAccountData,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\GiftCardAccount\Model\Resource\Giftcardaccount $resource,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\CustomerBalance\Model\Balance $customerBalance,
        \Magento\Framework\Stdlib\DateTime\DateTime $coreDate,
        \Magento\Framework\Locale\CurrencyInterface $localeCurrency,
        \Magento\Framework\StoreManagerInterface $storeManager,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\GiftCardAccount\Model\PoolFactory $poolFactory,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Framework\Data\Collection\Db $resourceCollection = null,
        array $data = array()
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->_giftCardAccountData = $giftCardAccountData;
        $this->_scopeConfig = $scopeConfig;
        $this->_transportBuilder = $transportBuilder;
        $this->_customerBalance = $customerBalance;
        $this->_coreDate = $coreDate;
        $this->_storeManager = $storeManager;
        $this->_checkoutSession = $checkoutSession;
        $this->_customerSession = $customerSession;
        $this->_localeCurrency = $localeCurrency;
        $this->_poolFactory = $poolFactory;
        $this->_localeDate = $localeDate;
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magento\GiftCardAccount\Model\Resource\Giftcardaccount');
    }

    /**
     * Processing object before save data
     *
     * @return $this
     * @throws \Magento\Framework\Model\Exception
     */
    protected function _beforeSave()
    {
        parent::_beforeSave();

        if (!$this->getId()) {
            $now = $this->_localeDate->date()->setTimezone(
                \Magento\Framework\Stdlib\DateTime\TimezoneInterface::DEFAULT_TIMEZONE
            )->toString(
                \Magento\Framework\Stdlib\DateTime::DATE_INTERNAL_FORMAT
            );

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
                $expirationDate = $this->_localeDate->date(
                    $this->getDateExpires(),
                    \Magento\Framework\Stdlib\DateTime::DATE_INTERNAL_FORMAT,
                    null,
                    false
                );
                $currentDate = $this->_localeDate->date(
                    null,
                    \Magento\Framework\Stdlib\DateTime::DATE_INTERNAL_FORMAT,
                    null,
                    false
                );
                if ($expirationDate < $currentDate) {
                    throw new \Magento\Framework\Model\Exception(__('An expiration date must be in the future.'));
                }
            } else {
                $this->setDateExpires(null);
            }
        }

        if (!$this->getId() && !$this->hasHistoryAction()) {
            $this->setHistoryAction(\Magento\GiftCardAccount\Model\History::ACTION_CREATED);
        }

        if (!$this->hasHistoryAction() && $this->getOrigData('balance') != $this->getBalance()) {
            $this->setHistoryAction(
                \Magento\GiftCardAccount\Model\History::ACTION_UPDATED
            )->setBalanceDelta(
                $this->getBalance() - $this->getOrigData('balance')
            );
        }
        if ($this->getBalance() < 0) {
            throw new \Magento\Framework\Model\Exception(__('The balance cannot be less than zero.'));
        }
    }

    /**
     * @return $this
     */
    protected function _afterSave()
    {
        if ($this->getIsNew()) {
            $this->getPoolModel()->setId(
                $this->getCode()
            )->setStatus(
                \Magento\GiftCardAccount\Model\Pool\AbstractPool::STATUS_USED
            )->save();
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
     * @return $this
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
     * @param \Magento\Sales\Model\Quote|null $quote
     * @return $this
     * @throws \Magento\Framework\Model\Exception
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
                        throw new \Magento\Framework\Model\Exception(__('This gift card account is already in the quote.'));
                    }
                }
            }
            $cards[] = array(
                'i' => $this->getId(),
                'c' => $this->getCode(),
                'a' => $this->getBalance(),
                'ba' => $this->getBalance()
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
     * @param \Magento\Sales\Model\Quote|null $quote
     * @return $this|void
     * @throws \Magento\Framework\Model\Exception
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
                $this->_throwException(__('Please correct the gift card account website: %1.', $this->getWebsiteId()));
            }
        }

        if ($statusCheck && $this->getStatus() != self::STATUS_ENABLED) {
            $this->_throwException(__('Gift card account %1 is not enabled.', $this->getId()));
        }

        if ($expirationCheck && $this->isExpired()) {
            $this->_throwException(__('Gift card account %1 is expired.', $this->getId()));
        }

        if ($balanceCheck) {
            if ($this->getBalance() <= 0) {
                $this->_throwException(__('Gift card account %1 has a zero balance.', $this->getId()));
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
     * @param float $amount
     * @return $this
     */
    public function charge($amount)
    {
        if ($this->isValid(false, false, false, $amount)) {
            $this->setBalanceDelta(
                -$amount
            )->setBalance(
                $this->getBalance() - $amount
            )->setHistoryAction(
                \Magento\GiftCardAccount\Model\History::ACTION_USED
            );
        }

        return $this;
    }

    /**
     * Revert amount to gift card balance if order was not placed
     *
     * @param   float $amount
     * @return  $this
     */
    public function revert($amount)
    {
        $amount = (double)$amount;

        if ($amount > 0 && $this->isValid(true, true, false, false)) {
            $this->setBalanceDelta(
                $amount
            )->setBalance(
                $this->getBalance() + $amount
            )->setHistoryAction(
                \Magento\GiftCardAccount\Model\History::ACTION_UPDATED
            );
        }

        return $this;
    }

    /**
     * Set state text on after load
     *
     * @return $this
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
        $result[self::STATE_USED] = __('Used');
        $result[self::STATE_REDEEMED] = __('Redeemed');
        $result[self::STATE_EXPIRED] = __('Expired');

        return $result;
    }

    /**
     * Retrieve pool model instance
     *
     * @return \Magento\GiftCardAccount\Model\Pool\AbstractPool
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
     * @return $this
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
     * @return $this
     * @throws \Magento\Framework\Model\Exception
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
                throw new \Magento\Framework\Model\Exception(__('You supplied an invalid customer ID.'));
            }

            $additionalInfo = __('Gift Card Redeemed: %1. For customer #%2.', $this->getCode(), $customerId);

            $balance = $this->_customerBalance->setCustomerId(
                $customerId
            )->setWebsiteId(
                $this->_storeManager->getWebsite()->getId()
            )->setAmountDelta(
                $this->getBalance()
            )->setNotifyByEmail(
                false
            )->setUpdatedActionAdditionalInfo(
                $additionalInfo
            )->save();

            $this->setBalanceDelta(
                -$this->getBalance()
            )->setHistoryAction(
                \Magento\GiftCardAccount\Model\History::ACTION_REDEEMED
            )->setBalance(
                0
            )->setCustomerId(
                $customerId
            )->save();
        }

        return $this;
    }

    /**
     * @return void
     */
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

        $balance = $this->_localeCurrency->getCurrency($recipientStore->getBaseCurrencyCode())->toCurrency($balance);

        $transport = $this->_transportBuilder->setTemplateIdentifier(
            $this->_scopeConfig->getValue(
                'giftcard/giftcardaccount_email/template',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $storeId
            )
        )->setTemplateOptions(
            array('area' => \Magento\Framework\App\Area::AREA_FRONTEND, 'store' => $storeId)
        )->setTemplateVars(
            array(
                'name' => $recipientName,
                'code' => $code,
                'balance' => $balance,
                'store' => $recipientStore,
                'store_name' => $recipientStore->getName()
            )
        )->setFrom(
            $this->_scopeConfig->getValue(
                'giftcard/giftcardaccount_email/identity',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $storeId
            )
        )->addTo(
            $recipientEmail,
            $recipientName
        )->getTransport();


        try {
            $transport->sendMessage();
            $this->setEmailSent(true)->setHistoryAction(\Magento\GiftCardAccount\Model\History::ACTION_SENT)->save();
        } catch (\Magento\Framework\Mail\Exception $e) {
            $this->setEmailSent(false);
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
     * @param string $realMessage
     * @param string $fakeMessage
     * @return void
     * @throws \Magento\Framework\Model\Exception
     */
    protected function _throwException($realMessage, $fakeMessage = '')
    {
        $e = new \Magento\Framework\Model\Exception($realMessage);
        $this->_logger->logException($e);
        if (!$fakeMessage) {
            $fakeMessage = __('Please correct the gift card code.');
        }
        $e->setMessage((string)$fakeMessage);
        throw $e;
    }
}
