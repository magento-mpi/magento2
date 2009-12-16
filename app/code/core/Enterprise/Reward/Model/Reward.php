<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
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
 * @category    Enterprise
 * @package     Enterprise_Reward
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */


/**
 * Reward model
 *
 * @category    Enterprise
 * @package     Enterprise_Reward
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Reward_Model_Reward extends Mage_Core_Model_Abstract
{
    const XML_PATH_MIN_POINTS_BALANCE = 'enterprise_reward/general/min_points_balance';
    const XML_PATH_MAX_POINTS_BALANCE = 'enterprise_reward/general/max_points_balance';

    const XML_PATH_BALANCE_UPDATE_TEMPLATE = 'enterprise_reward/notification/balance_update_template';
    const XML_PATH_BALANCE_WARNING_TEMPLATE = 'enterprise_reward/notification/expiry_warning_template';
    const XML_PATH_EMAIL_IDENTITY = 'enterprise_reward/notification/email_sender';

    const REWARD_ACTION_ADMIN               = 0;
    const REWARD_ACTION_ORDER               = 1;
    const REWARD_ACTION_REGISTER            = 2;
    const REWARD_ACTION_NEWSLETTER          = 3;
    const REWARD_ACTION_INVITATION_CUSTOMER = 4;
    const REWARD_ACTION_INVITATION_ORDER    = 5;
    const REWARD_ACTION_REVIEW              = 6;
    const REWARD_ACTION_TAG                 = 7;
    const REWARD_ACTION_ORDER_EXTRA         = 8;

    protected $_rates = array();

    /**
     * Internal constructor
     */
    protected function _construct()
    {
        $this->_init('enterprise_reward/reward');
    }

    /**
     * Processing object before save data.
     * Load model by customer and website,
     * prepare points data
     *
     * @return Enterprise_Reward_Model_Reward
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
     * @return Enterprise_Reward_Model_Reward
     */
    protected function _afterSave()
    {
        $this->_prepareCurrencyAmount();
        $this->getHistory()->setReward($this)
            ->prepareFromReward()
            ->save();
        return parent::_afterSave();
    }

    /**
     * Check if can update reward
     *
     * @return boolean
     */
    public function canUpdateRewardPoints()
    {
        $result = true;
        switch ($this->getAction()) {
            case self::REWARD_ACTION_REVIEW:
                $this->getHistory()->setEntity($this->getReview()->getId());
                $result = !($this->getHistory()->isExistHistoryUpdate($this->getCustomerId(), $this->getAction(),
                    $this->getWebsiteId(), $this->getReview()->getId()));
                break;
            case self::REWARD_ACTION_TAG:
                $this->getHistory()->setEntity($this->getTag()->getId());
                $result = !($this->getHistory()->isExist($this->getCustomerId(), $this->getAction(),
                    $this->getWebsiteId(), $this->getTag()->getId()));
                break;
            case self::REWARD_ACTION_REGISTER:
                $this->getHistory()->setEntity($this->getCustomer()->getId());
                $result = !((bool)$this->loadByCustomer()->getId());
                break;
            case self::REWARD_ACTION_ORDER:
                $this->getHistory()->setEntity($this->getOrder()->getId());
                break;
        }
        return $result;
    }

    /**
     * Save reward points
     *
     * @return Enterprise_Reward_Model_Reward
     */
    public function updateRewardPoints()
    {
        if ($this->canUpdateRewardPoints()) {
            $this->save();
        }
        return $this;
    }

    /**
     * Setter.
     * Set customer id
     *
     * @param Mage_Customer_Model_Customer $customer
     * @return Enterprise_Reward_Model_Reward
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
     * @return Mage_Customer_Model_Customer
     */
    public function getCustomer()
    {
        if (!$this->getData('customer') && $this->getCustomerId()) {
            $customer = Mage::getModel('customer/customer')->load($this->getCustomerId());
            $this->setCustomer($customer);
        }
        return $this->getData('customer');
    }

    /**
     * Getter
     *
     * @return integer
     */
    public function getCustomerGroupId()
    {
        if (!$this->getData('customer_group_id') && $this->getCustomer()) {
            $this->setData('customer_group_id', $this->getCustomer()->getGroupId());
        }
        return $this->getData('customer_group_id');
    }

    /**
     * Getter for website_id
     * If website id not set, get it from assigned store
     *
     * @return int
     */
    public function getWebsiteId()
    {
        if (!$this->getData('website_id') && ($store = $this->getStore())) {
            $this->setData('website_id', $store->getWebsiteId());
        }
        return $this->getData('website_id');
    }

    /**
     * Getter for store (for emails etc)
     * Trying get store from customer if its not assigned
     *
     * @return Mage_Core_Model_Store|null
     */
    public function getStore()
    {
        $store = null;
        if ($this->hasData('store') || $this->hasData('store_id')) {
            $store = $this->getDataSetDefault('store', $this->_getData('store_id'));
        } elseif ($this->getCustomer()) {
            $store = $this->getCustomer()->getStore();
            $this->setData('store', $store);
        }
        if ($store !== null) {
            return is_object($store) ? $store : Mage::app()->getStore($store);
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
        if ($this->getData('points_delta') === null) {
            $this->_preparePointsDelta();
        }
        return $this->getData('points_delta');
    }

    /**
     * Getter.
     * Recalculate currency amount if need.
     *
     * @return float
     */
    public function getCurrencyAmount()
    {
        if ($this->getData('currency_amount') === null) {
            $this->_prepareCurrencyAmount();
        }
        return $this->getData('currency_amount');
    }

    /**
     * Getter.
     * Return formated currency amount in currency of website
     *
     * @return string
     */
    public function getFormatedCurrencyAmount()
    {
        $currencyAmount = Mage::app()->getLocale()->currency($this->getWebsiteCurrencyCode())
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
        if (!$this->getData('website_currency_code')) {
            $this->setData('website_currency_code', Mage::app()->getWebsite($this->getWebsiteId())
                ->getBaseCurrencyCode());
        }
        return $this->getData('website_currency_code');
    }

    /**
     * Getter
     *
     * @return Enterprise_Reward_Model_Reward_Config
     */
    public function getConfig()
    {
        if (!$this->getData('config')) {
            $config = Mage::getModel('enterprise_reward/reward_config');
            $this->setData('config', $config);
        }
        return $this->getData('config');
    }

    /**
     * Getter
     *
     * @return Enterprise_Reward_Model_Reward_History
     */
    public function getHistory()
    {
        if (!$this->getData('history')) {
            $this->setData('history', Mage::getModel('enterprise_reward/reward_history'));
        }
        return $this->getData('history');
    }

    /**
     * Initialize and fetch if need rate by given direction
     *
     * @param integer $direction
     * @return Enterprise_Reward_Model_Reward_Rate
     */
    protected function _getRateByDirection($direction)
    {
        if (!isset($this->_rates[$direction])) {
            $this->_rates[$direction] = Mage::getModel('enterprise_reward/reward_rate')
                ->fetch($this->getCustomerGroupId(), $this->getWebsiteId(), $direction);
        }
        return $this->_rates[$direction];
    }

    /**
     * Return rate depend on action
     *
     * @return Enterprise_Reward_Model_Reward_Rate
     */
    public function getRate()
    {
        return $this->_getRateByDirection($this->getRateDirectionByAction());
    }

    /**
     * Return rate to convert points to currency amount
     *
     * @return Enterprise_Reward_Model_Reward_Rate
     */
    public function getRateToCurrency()
    {
        return $this->_getRateByDirection(Enterprise_Reward_Model_Reward_Rate::RATE_EXCHANGE_DIRECTION_TO_CURRENCY);
    }

    /**
     * Return rate to convert currency amount to points
     *
     * @return Enterprise_Reward_Model_Reward_Rate
     */
    public function getRateToPoints()
    {
        return $this->_getRateByDirection(Enterprise_Reward_Model_Reward_Rate::RATE_EXCHANGE_DIRECTION_TO_POINTS);
    }

    /**
     * Return rate direction by action
     *
     * @return integer
     */
    public function getRateDirectionByAction()
    {
        switch($this->getAction()) {
            case self::REWARD_ACTION_ORDER:
                $direction = Enterprise_Reward_Model_Reward_Rate::RATE_EXCHANGE_DIRECTION_TO_POINTS;
                break;
            default:
                $direction = Enterprise_Reward_Model_Reward_Rate::RATE_EXCHANGE_DIRECTION_TO_CURRENCY;
                break;
        }
        return $direction;
    }

    /**
     * Load by customer and website
     *
     * @return Enterprise_Reward_Model_Reward
     */
    public function loadByCustomer()
    {
        if ($this->getCustomerId() && $this->getWebsiteId()) {
            $this->getResource()->loadByCustomerId($this,
                $this->getCustomerId(), $this->getWebsiteId());
        }
        return $this;
    }

    /**
     * Prepare points delta, get points delta from config by action
     *
     * @return Enterprise_Reward_Model_Reward
     */
    protected function _preparePointsDelta()
    {
        $delta = 0;
        $delta = $this->getConfig()->getPointsDeltaByAction($this->getAction(), $this->getWebsiteId());
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
     * @return Enterprise_Reward_Model_Reward
     */
    protected function _preparePointsBalance()
    {
        $points = 0;
        if ($this->hasPointsDelta()) {
            $points = $this->getPointsDelta();
        }
        if ($this->getId()) {
            $this->setPointsBalance($this->getPointsBalance() + $points);
        } else {
            $this->setPointsBalance($points);
        }
        return $this;
    }

    /**
     * Prepare currency amount and currency delta
     *
     * @return Enterprise_Reward_Model_Reward
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
        $ammount = 0;
        if ($points && $this->getRateToCurrency()) {
            $ammount = $this->getRateToCurrency()->calculateToCurrency($points);
        }
        return (float)$ammount;
    }

    /**
     * Convert currency amount to points
     *
     * @param float $amount
     * @return integer
     */
    protected function _convertCurrencyToPoints($amount)
    {
        $points = 0;
        return $points;
    }

    /**
     * Check is enough points (currency amount) to cover given amount
     *
     * @param float $amount
     * @return boolean
     */
    public function isEnoughPointsToCoverAmount($amount)
    {
        $result = false;
        if ($this->getId()) {
            if ($this->getCurrencyAmount() >= $amount) {
                $result = true;
            }
        }
        return $result;
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
        if ($amount) {
            $ratePointsCount = $this->getRateToCurrency()->getPoints();
            $rateCurrencyAmount = $this->getRateToCurrency()->getCurrencyAmount();
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
     * @return Enterprise_Reward_Model_Reward
     */
    public function sendBalanceUpdateNotification()
    {
        if (!$this->getCustomer()->getRewardUpdateNotification()) {
            return $this;
        }
        $store = Mage::app()->getStore($this->getStore());
        $mail  = Mage::getModel('core/email_template');
        /* @var $mail Mage_Core_Model_Email_Template */
        $mail->setDesignConfig(array('area' => 'frontend', 'store' => $store->getId()));
        $templateVars = array(
            'store' => $store,
            'customer' => $this->getCustomer(),
            'unsubscription_url' => Mage::helper('enterprise_reward/customer')->getUnsubscribeUrl('update'),
            'points_balance' => $this->getPointsBalance()
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
     * @return Enterprise_Reward_Model_Reward
     */
    public function sendBalanceWarningNotification()
    {
        if (!$this->getCustomer()->getRewardWarningNotification()) {
            return $this;
        }
        $store = Mage::app()->getStore($this->getStore());
        $mail  = Mage::getModel('core/email_template');
        /* @var $mail Mage_Core_Model_Email_Template */
        $mail->setDesignConfig(array('area' => 'frontend', 'store' => $store->getId()));
        $templateVars = array(
            'store' => $store,
            'customer' => $this->getCustomer(),
            'unsubscription_url' => Mage::helper('enterprise_reward/customer')->getUnsubscribeUrl('warning'),
            'remaining_days' => $store->getConfig('enterprise_reward/notification/expiry_day_before'),
            'points_balance' => $this->getPointsBalance(),
            'points_expiring' => $this->getPointsBalance()
        );
        $mail->sendTransactional(
            $store->getConfig(self::XML_PATH_BALANCE_WARNING_TEMPLATE),
            $store->getConfig(self::XML_PATH_EMAIL_IDENTITY),
            $this->getCustomer()->getEmail(),
            null,
            $templateVars,
            $store->getId()
        );
        if ($mail->getSentSuccess()) {
            $this->setBalanceWarningSent(true);
        }
        return $this;
    }
}
