<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Reward
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Reward model
 *
 * @method \Magento\Reward\Model\Resource\Reward _getResource()
 * @method \Magento\Reward\Model\Resource\Reward getResource()
 * @method int getCustomerId()
 * @method \Magento\Reward\Model\Reward setCustomerId(int $value)
 * @method \Magento\Reward\Model\Reward setWebsiteId(int $value)
 * @method int getPointsBalance()
 * @method \Magento\Reward\Model\Reward setPointsBalance(int $value)
 * @method \Magento\Reward\Model\Reward setWebsiteCurrencyCode(string $value)
 * @method \Magento\Reward\Model\Reward setPointsDelta() setPointsDelta(int $value)
 * @method \Magento\Reward\Model\Reward setAction() setAction(int $value)
 * @method \Magento\Reward\Model\Reward setComment() setComment(string $value)
 */
namespace Magento\Reward\Model;

class Reward extends \Magento\Core\Model\AbstractModel
{
    const XML_PATH_BALANCE_UPDATE_TEMPLATE = 'magento_reward/notification/balance_update_template';
    const XML_PATH_BALANCE_WARNING_TEMPLATE = 'magento_reward/notification/expiry_warning_template';
    const XML_PATH_EMAIL_IDENTITY = 'magento_reward/notification/email_sender';
    const XML_PATH_MIN_POINTS_BALANCE = 'magento_reward/general/min_points_balance';

    const REWARD_ACTION_ADMIN               = 0;
    const REWARD_ACTION_ORDER               = 1;
    const REWARD_ACTION_REGISTER            = 2;
    const REWARD_ACTION_NEWSLETTER          = 3;
    const REWARD_ACTION_INVITATION_CUSTOMER = 4;
    const REWARD_ACTION_INVITATION_ORDER    = 5;
    const REWARD_ACTION_REVIEW              = 6;
    const REWARD_ACTION_ORDER_EXTRA         = 8;
    const REWARD_ACTION_CREDITMEMO          = 9;
    const REWARD_ACTION_SALESRULE           = 10;
    const REWARD_ACTION_REVERT              = 11;

    protected $_modelLoadedByCustomer = false;

    static protected $_actionModelClasses = array();

    protected $_rates = array();

    /**
     * Identifies that reward balance was updated or not
     *
     * @var boolean
     */
    protected $_rewardPointsUpdated = false;

    /**
     * Reward data
     *
     * @var Magento_Reward_Helper_Data
     */
    protected $_rewardData = null;

    /**
     * Reward customer
     *
     * @var Magento_Reward_Helper_Customer
     */
    protected $_rewardCustomer = null;

    /**
     * @param Magento_Reward_Helper_Customer $rewardCustomer
     * @param Magento_Reward_Helper_Data $rewardData
     * @param Magento_Core_Model_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param Magento_Core_Model_Resource_Abstract $resource
     * @param Magento_Data_Collection_Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        Magento_Reward_Helper_Customer $rewardCustomer,
        Magento_Reward_Helper_Data $rewardData,
        Magento_Core_Model_Context $context,
        Magento_Core_Model_Registry $registry,
        Magento_Core_Model_Resource_Abstract $resource = null,
        Magento_Data_Collection_Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_rewardCustomer = $rewardCustomer;
        $this->_rewardData = $rewardData;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Internal constructor
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('Magento\Reward\Model\Resource\Reward');
        self::$_actionModelClasses = self::$_actionModelClasses + array(
            self::REWARD_ACTION_ADMIN               => 'Magento\Reward\Model\Action\Admin',
            self::REWARD_ACTION_ORDER               => 'Magento\Reward\Model\Action\Order',
            self::REWARD_ACTION_REGISTER            => 'Magento\Reward\Model\Action\Register',
            self::REWARD_ACTION_NEWSLETTER          => 'Magento\Reward\Model\Action\Newsletter',
            self::REWARD_ACTION_INVITATION_CUSTOMER => 'Magento\Reward\Model\Action\InvitationCustomer',
            self::REWARD_ACTION_INVITATION_ORDER    => 'Magento\Reward\Model\Action\InvitationOrder',
            self::REWARD_ACTION_REVIEW              => 'Magento\Reward\Model\Action\Review',
            self::REWARD_ACTION_ORDER_EXTRA         => 'Magento\Reward\Model\Action\OrderExtra',
            self::REWARD_ACTION_CREDITMEMO          => 'Magento\Reward\Model\Action\Creditmemo',
            self::REWARD_ACTION_SALESRULE           => 'Magento\Reward\Model\Action\Salesrule',
            self::REWARD_ACTION_REVERT              => 'Magento\Reward\Model\Action\OrderRevert'
        );
    }

    /**
     * Set action Id and action model class.
     * Check if given action Id is not integer throw exception
     *
     * @param integer $actionId
     * @param string $actionModelClass
     */
    public static function setActionModelClass($actionId, $actionModelClass)
    {
        if (!is_int($actionId)) {
            \Mage::throwException(__('The action ID you enter must be a numerical integer.'));
        }
        self::$_actionModelClasses[$actionId] = $actionModelClass;
    }

    /**
     * Processing object before save data.
     * Load model by customer and website,
     * prepare points data
     *
     * @return \Magento\Reward\Model\Reward
     */
    protected function _beforeSave()
    {
        $this->loadByCustomer()
            ->_preparePointsDelta()
            ->_preparePointsBalance();
        return parent::_beforeSave();
    }

    /**
     * Processing object after save data.
     * Save reward history
     *
     * @return \Magento\Reward\Model\Reward
     */
    protected function _afterSave()
    {
        if ((int)$this->getPointsDelta() != 0 || $this->getCappedReward()) {
            $this->_prepareCurrencyAmount();
            $this->getHistory()
                ->prepareFromReward()
                ->save();
            $this->sendBalanceUpdateNotification();
        }
        return parent::_afterSave();
    }

    /**
     * Return instance of action wrapper
     *
     * @param string|int $action Action code or a factory name
     * @param bool $isFactoryName
     * @return Magento_Reward_Model_Action_Abstract|null
     */
    public function getActionInstance($action, $isFactoryName = false)
    {
        if ($isFactoryName) {
            $action = array_search($action, self::$_actionModelClasses);
            if (!$action) {
                return null;
            }
        }
        $instance = $this->_coreRegistry->registry('_reward_actions' . $action);
        if (!$instance && array_key_exists($action, self::$_actionModelClasses)) {
            $instance = \Mage::getModel(self::$_actionModelClasses[$action]);
            // setup invariant properties once
            $instance->setAction($action);
            $instance->setReward($this);
            $this->_coreRegistry->register('_reward_actions' . $action, $instance);
        }
        if (!$instance) {
            return null;
        }
        // keep variable properties up-to-date
        $instance->setHistory($this->getHistory());
        if ($this->getActionEntity()) {
            $instance->setEntity($this->getActionEntity());
        }
        return $instance;
    }

    /**
     * Check if can update reward
     *
     * @return boolean
     */
    public function canUpdateRewardPoints()
    {
        return $this->getActionInstance($this->getAction())->canAddRewardPoints();
    }

    /**
     * Getter
     *
     * @return boolean
     */
    public function getRewardPointsUpdated()
    {
        return $this->_rewardPointsUpdated;
    }

    /**
     * Save reward points
     *
     * @return \Magento\Reward\Model\Reward
     * @throws Exception
     */
    public function updateRewardPoints()
    {
        $this->_rewardPointsUpdated = false;
        if ($this->canUpdateRewardPoints()) {
            try {
                $this->save();
                $this->_rewardPointsUpdated = true;
            } catch (\Exception $e) {
                $this->_rewardPointsUpdated = false;
                throw $e;
            }
        }
        return $this;
    }

    /**
     * Setter.
     * Set customer id
     *
     * @param \Magento\Customer\Model\Customer $customer
     * @return \Magento\Reward\Model\Reward
     */
    public function setCustomer($customer)
    {
        $this->setData('customer_id', $customer->getId());
        $this->setData('customer_group_id', $customer->getGroupId());
        $this->setData('customer', $customer);
        return $this;
    }

    /**
     * Getter
     *
     * @return \Magento\Customer\Model\Customer
     */
    public function getCustomer()
    {
        if (!$this->_getData('customer') && $this->getCustomerId()) {
            $customer = \Mage::getModel('Magento\Customer\Model\Customer')->load($this->getCustomerId());
            $this->setCustomer($customer);
        }
        return $this->_getData('customer');
    }

    /**
     * Getter
     *
     * @return integer
     */
    public function getCustomerGroupId()
    {
        if (!$this->_getData('customer_group_id') && $this->getCustomer()) {
            $this->setData('customer_group_id', $this->getCustomer()->getGroupId());
        }
        return $this->_getData('customer_group_id');
    }

    /**
     * Getter for website_id
     * If website id not set, get it from assigned store
     *
     * @return int
     */
    public function getWebsiteId()
    {
        if (!$this->_getData('website_id') && ($store = $this->getStore())) {
            $this->setData('website_id', $store->getWebsiteId());
        }
        return $this->_getData('website_id');
    }

    /**
     * Getter for store (for emails etc)
     * Trying get store from customer if its not assigned
     *
     * @return \Magento\Core\Model\Store|null
     */
    public function getStore()
    {
        $store = null;
        if ($this->hasData('store') || $this->hasData('store_id')) {
            $store = $this->getDataSetDefault('store', $this->_getData('store_id'));
        } elseif ($this->getCustomer() && $this->getCustomer()->getStoreId()) {
            $store = $this->getCustomer()->getStore();
            $this->setData('store', $store);
        }
        if ($store !== null) {
            return is_object($store) ? $store : \Mage::app()->getStore($store);
        }
        return $store;
    }

    /**
     * Getter
     *
     * @return integer
     */
    public function getPointsDelta()
    {
        if ($this->_getData('points_delta') === null) {
            $this->_preparePointsDelta();
        }
        return $this->_getData('points_delta');
    }

    /**
     * Getter.
     * Recalculate currency amount if need.
     *
     * @return float
     */
    public function getCurrencyAmount()
    {
        if ($this->_getData('currency_amount') === null) {
            $this->_prepareCurrencyAmount();
        }
        return $this->_getData('currency_amount');
    }

    /**
     * Getter.
     * Return formated currency amount in currency of website
     *
     * @return string
     */
    public function getFormatedCurrencyAmount()
    {
        $currencyAmount = \Mage::app()->getLocale()->currency($this->getWebsiteCurrencyCode())
                ->toCurrency($this->getCurrencyAmount());
        return $currencyAmount;
    }

    /**
     * Getter
     *
     * @return string
     */
    public function getWebsiteCurrencyCode()
    {
        if (!$this->_getData('website_currency_code')) {
            $this->setData('website_currency_code', \Mage::app()->getWebsite($this->getWebsiteId())
                ->getBaseCurrencyCode());
        }
        return $this->_getData('website_currency_code');
    }

    /**
     * Getter
     *
     * @return \Magento\Reward\Model\Reward\History
     */
    public function getHistory()
    {
        if (!$this->_getData('history')) {
            $this->setData('history', \Mage::getModel('Magento\Reward\Model\Reward\History'));
            $this->getHistory()->setReward($this);
        }
        return $this->_getData('history');
    }

    /**
     * Initialize and fetch if need rate by given direction
     *
     * @param integer $direction
     * @return \Magento\Reward\Model\Reward\Rate
     */
    protected function _getRateByDirection($direction)
    {
        if (!isset($this->_rates[$direction])) {
            $this->_rates[$direction] = \Mage::getModel('Magento\Reward\Model\Reward\Rate')
                ->fetch($this->getCustomerGroupId(), $this->getWebsiteId(), $direction);
        }
        return $this->_rates[$direction];
    }

    /**
     * Return rate depend on action
     *
     * @return \Magento\Reward\Model\Reward\Rate
     */
    public function getRate()
    {
        return $this->_getRateByDirection($this->getRateDirectionByAction());
    }

    /**
     * Return rate to convert points to currency amount
     *
     * @return \Magento\Reward\Model\Reward\Rate
     */
    public function getRateToCurrency()
    {
        return $this->_getRateByDirection(\Magento\Reward\Model\Reward\Rate::RATE_EXCHANGE_DIRECTION_TO_CURRENCY);
    }

    /**
     * Return rate to convert currency amount to points
     *
     * @return \Magento\Reward\Model\Reward\Rate
     */
    public function getRateToPoints()
    {
        return $this->_getRateByDirection(\Magento\Reward\Model\Reward\Rate::RATE_EXCHANGE_DIRECTION_TO_POINTS);
    }

    /**
     * Return rate direction by action
     *
     * @return integer
     */
    public function getRateDirectionByAction()
    {
        switch($this->getAction()) {
            case self::REWARD_ACTION_ORDER_EXTRA:
                $direction = \Magento\Reward\Model\Reward\Rate::RATE_EXCHANGE_DIRECTION_TO_POINTS;
                break;
            default:
                $direction = \Magento\Reward\Model\Reward\Rate::RATE_EXCHANGE_DIRECTION_TO_CURRENCY;
                break;
        }
        return $direction;
    }

    /**
     * Load by customer and website
     *
     * @return \Magento\Reward\Model\Reward
     */
    public function loadByCustomer()
    {
        if (!$this->_modelLoadedByCustomer && $this->getCustomerId() && $this->getWebsiteId()) {
            $this->getResource()->loadByCustomerId($this,
                $this->getCustomerId(), $this->getWebsiteId());
            $this->_modelLoadedByCustomer = true;
        }
        return $this;
    }

    /**
     * Estimate available points reward for specified action
     *
     * @param \Magento\Reward\Model\Action\AbstractAction $action
     * @return int|null
     */
    public function estimateRewardPoints(\Magento\Reward\Model\Action\AbstractAction $action)
    {
        $websiteId = $this->getWebsiteId();
        $uncappedPts = (int)$action->getPoints($websiteId);
        $max = (int)$this->_rewardData->getGeneralConfig('max_points_balance', $websiteId);
        if ($max > 0) {
            return min(max($max - (int)$this->getPointsBalance(), 0), $uncappedPts);
        }
        return $uncappedPts;
    }

    /**
     * Estimate available monetary reward for specified action
     * May take points value or automatically determine from action
     *
     * @param \Magento\Reward\Model\Action\AbstractAction $action
     * @return float|null
     */
    public function estimateRewardAmount(\Magento\Reward\Model\Action\AbstractAction $action)
    {
        if (!$this->getCustomerId()) {
            return null;
        }
        $this->getWebsiteId();
        $rate = $this->getRateToCurrency();
        if (!$rate->getId()) {
            return null;
        }
        return $rate->calculateToCurrency($this->estimateRewardPoints($action), false);
    }

    /**
     * Prepare points delta, get points delta from config by action
     *
     * @return \Magento\Reward\Model\Reward
     */
    protected function _preparePointsDelta()
    {
        $delta = 0;
        $action = $this->getActionInstance($this->getAction());
        if ($action !== null) {
            $delta = $action->getPoints($this->getWebsiteId());
        }
        if ($delta) {
            if ($this->hasPointsDelta()) {
                $delta = $delta + $this->getPointsDelta();
            }
            $this->setPointsDelta((int)$delta);
        }
        return $this;
    }

    /**
     * Prepare points balance
     *
     * @return \Magento\Reward\Model\Reward
     */
    protected function _preparePointsBalance()
    {
        $points = 0;
        if ($this->hasPointsDelta()) {
            $points = $this->getPointsDelta();
        }
        $pointsBalance = 0;
        $pointsBalance = (int)$this->getPointsBalance() + $points;
        $maxPointsBalance = (int)($this->_rewardData
            ->getGeneralConfig('max_points_balance', $this->getWebsiteId()));
        if ($maxPointsBalance != 0 && ($pointsBalance > $maxPointsBalance)) {
            $pointsBalance = $maxPointsBalance;
            $pointsDelta   = $maxPointsBalance - (int)$this->getPointsBalance();
            $croppedPoints = (int)$this->getPointsDelta() - $pointsDelta;
            $this->setPointsDelta($pointsDelta)
                ->setIsCappedReward(true)
                ->setCroppedPoints($croppedPoints);
        }
        $this->setPointsBalance($pointsBalance);
        return $this;
    }

    /**
     * Prepare currency amount and currency delta
     *
     * @return \Magento\Reward\Model\Reward
     */
    protected function _prepareCurrencyAmount()
    {
        $amount = 0;
        $amountDelta = 0;
        if ($this->hasPointsDelta()) {
            $amountDelta = $this->_convertPointsToCurrency($this->getPointsDelta());
        }
        $amount = $this->_convertPointsToCurrency($this->getPointsBalance());
        $this->setCurrencyDelta((float)$amountDelta);
        $this->setCurrencyAmount((float)($amount));
        return $this;
    }

    /**
     * Convert points to currency
     *
     * @param integer $points
     * @return float
     */
    protected function _convertPointsToCurrency($points)
    {
        return $points && $this->getRateToCurrency()
            ? (float)$this->getRateToCurrency()->calculateToCurrency($points)
            : 0;
    }

    /**
     * Check is enough points (currency amount) to cover given amount
     *
     * @param float $amount
     * @return bool
     */
    public function isEnoughPointsToCoverAmount($amount)
    {
        return $this->getId() && $this->getCurrencyAmount() >= $amount;
    }

    /**
     * Return points equivalent of given amount.
     * Converting by 'to currency' rate and points round up
     *
     * @param float $amount
     * @return integer
     */
    public function getPointsEquivalent($amount)
    {
        $points = 0;
        if (!$amount) {
            return $points;
        }

        $ratePointsCount = $this->getRateToCurrency()->getPoints();
        $rateCurrencyAmount = $this->getRateToCurrency()->getCurrencyAmount();
        if ($rateCurrencyAmount > 0) {
            $delta = $amount / $rateCurrencyAmount;
            if ($delta > 0) {
                $points = $ratePointsCount * ceil($delta);
            }
        }

        return $points;
    }

    /**
     * Send Balance Update Notification to customer if notification is enabled
     *
     * @return \Magento\Reward\Model\Reward
     */
    public function sendBalanceUpdateNotification()
    {
        if (!$this->getCustomer()->getRewardUpdateNotification()) {
            return $this;
        }
        $delta = (int)$this->getPointsDelta();
        if ($delta == 0) {
            return $this;
        }
        $history = $this->getHistory();
        $store = \Mage::app()->getStore($this->getStore());
        $mail  = \Mage::getModel('Magento\Core\Model\Email\Template');
        /* @var $mail \Magento\Core\Model\Email\Template */
        $mail->setDesignConfig(array('area' => \Magento\Core\Model\App\Area::AREA_FRONTEND, 'store' => $store->getId()));
        $templateVars = array(
            'store' => $store,
            'customer' => $this->getCustomer(),
            'unsubscription_url' => $this->_rewardCustomer
                ->getUnsubscribeUrl('update', $store->getId()),
            'points_balance' => $this->getPointsBalance(),
            'reward_amount_was' => $this->_rewardData->formatAmount(
                $this->getCurrencyAmount() - $history->getCurrencyDelta(), true, $store->getStoreId()
            ),
            'reward_amount_now' => $this->_rewardData->formatAmount(
                $this->getCurrencyAmount(), true, $store->getStoreId()
            ),
            'reward_pts_was' => ($this->getPointsBalance() - $delta),
            'reward_pts_change' => $delta,
            'update_message' => $this->getHistory()->getMessage(),
            'update_comment' => $history->getComment()
        );
        $mail->sendTransactional(
            $store->getConfig(self::XML_PATH_BALANCE_UPDATE_TEMPLATE),
            $store->getConfig(self::XML_PATH_EMAIL_IDENTITY),
            $this->getCustomer()->getEmail(),
            null,
            $templateVars,
            $store->getId()
        );
        if ($mail->getSentSuccess()) {
            $this->setBalanceUpdateSent(true);
        }
        return $this;
    }

    /**
     * Send low Balance Warning Notification to customer if notification is enabled
     *
     * @param object $item
     * @param int $websiteId
     * @return \Magento\Reward\Model\Reward
     * @see \Magento\Reward\Model\Resource\Reward\History\Collection::loadExpiredSoonPoints()
     */
    public function sendBalanceWarningNotification($item, $websiteId)
    {
        $mail  = \Mage::getModel('Magento\Core\Model\Email\Template');
        /* @var $mail \Magento\Core\Model\Email\Template */
        $mail->setDesignConfig(array(
            'area' => \Magento\Core\Model\App\Area::AREA_FRONTEND,
            'store' => $item->getStoreId()
        ));
        $store = \Mage::app()->getStore($item->getStoreId());
        $helper = $this->_rewardData;
        $amount = $helper
            ->getRateFromRatesArray($item->getPointsBalanceTotal(), $websiteId, $item->getCustomerGroupId());
        $action = \Mage::getSingleton('Magento\Reward\Model\Reward')->getActionInstance($item->getAction());
        $templateVars = array(
            'store' => $store,
            'customer_name' => $item->getCustomerFirstname().' '.$item->getCustomerLastname(),
            'unsubscription_url' => $this->_rewardCustomer->getUnsubscribeUrl('warning'),
            'remaining_days' => $store->getConfig('magento_reward/notification/expiry_day_before'),
            'points_balance' => $item->getPointsBalanceTotal(),
            'points_expiring' => $item->getTotalExpired(),
            'reward_amount_now' => $helper->formatAmount($amount, true, $item->getStoreId()),
            'update_message' => ($action !== null ? $action->getHistoryMessage($item->getAdditionalData()) : '')
        );
        $mail->sendTransactional(
            $store->getConfig(self::XML_PATH_BALANCE_WARNING_TEMPLATE),
            $store->getConfig(self::XML_PATH_EMAIL_IDENTITY),
            $item->getCustomerEmail(),
            null,
            $templateVars,
            $store->getId()
        );
        return $this;
    }

    /**
     * Prepare orphan points by given website id and website base currency code
     * after website was deleted
     *
     * @param integer $websiteId
     * @param string $baseCurrencyCode
     * @return \Magento\Reward\Model\Reward
     */
    public function prepareOrphanPoints($websiteId, $baseCurrencyCode)
    {
        if ($websiteId) {
            $this->_getResource()->prepareOrphanPoints($websiteId, $baseCurrencyCode);
        }
        return $this;
    }

    /**
     * Delete orphan (points of deleted website) points by given customer
     *
     * @param \Magento\Customer\Model\Customer | integer | null $customer
     * @return \Magento\Reward\Model\Reward
     */
    public function deleteOrphanPointsByCustomer($customer = null)
    {
        if ($customer === null) {
            $customer = $this->getCustomerId()?$this->getCustomerId():$this->getCustomer();
        }
        if (is_object($customer) && $customer instanceof \Magento\Customer\Model\Customer) {
            $customer = $customer->getId();
        }
        if ($customer) {
            $this->_getResource()->deleteOrphanPointsByCustomer($customer);
        }
        return $this;
    }

    /**
     * Override setter for setting customer group id  from order
     *
     * @param mixed $entity
     * @return Magento_Reward_Model_Reward
     */
    public function setActionEntity($entity)
    {
        if ($entity->getCustomerGroupId()) {
            $this->setCustomerGroupId($entity->getCustomerGroupId());
        }
        return parent::setData('action_entity', $entity);
    }
}
