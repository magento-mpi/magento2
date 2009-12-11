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
        Mage::getModel('enterprise_reward/reward_history')
            ->setReward($this)
            ->prepareFromReward()
            ->save();
        return parent::_afterSave();
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

    public function sendNotification()
    {
        return $this;
    }
}
