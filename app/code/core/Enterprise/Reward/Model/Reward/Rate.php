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
 * Reward rate model
 *
 * @category    Enterprise
 * @package     Enterprise_Reward
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Reward_Model_Reward_Rate extends Mage_Core_Model_Abstract
{
    const RATE_CUSTOMER_GROUP_ID_ALL          = 'all';
    const RATE_EXCHANGE_DIRECTION_TO_CURRENCY = 1;
    const RATE_EXCHANGE_DIRECTION_TO_POINTS   = 2;

    /**
     * Internal constructor
     */
    protected function _construct()
    {
        $this->_init('enterprise_reward/reward_rate');
    }

    /**
     * Processing object before save data.
     * Prepare rate data
     *
     * @return Enterprise_Reward_Model_Reward_Rate
     */
    protected function _beforeSave()
    {
        parent::_beforeSave();
        if ($this->getData('customer_group_id') == self::RATE_CUSTOMER_GROUP_ID_ALL) {
            $this->setData('customer_group_id', null);
        }
        $this->_prepareRateValues();
        return $this;
    }

    /**
     * Validate rate data
     *
     * @return boolean | string
     */
    public function validate()
    {
        return true;
    }

    /**
     * Processing object after load data.
     * Prepare rate data
     *
     * @return Enterprise_Reward_Model_Reward_Rate
     */
    protected function _afterLoad()
    {
        parent::_afterLoad();
        $this->prepareCustomerGroupValue();
        return $this;
    }

    /**
     * Prepare values in order to defined direction
     *
     * @return Enterprise_Reward_Model_Reward_Rate
     */
    protected function _prepareRateValues()
    {
        if ($this->getData('direction') == self::RATE_EXCHANGE_DIRECTION_TO_CURRENCY) {
            $this->setData('points', (int)$this->getData('value'));
            $this->setData('currency_amount', (float)$this->getData('equal_value'));
        } elseif ($this->getData('direction') == self::RATE_EXCHANGE_DIRECTION_TO_POINTS) {
            $this->setData('currency_amount', (float)$this->getData('value'));
            $this->setData('points', (int)$this->getData('equal_value'));
        }
        return $this;
    }

    /**
     * Fetch rate by customer group and website
     *
     * @param integer $customerGroupId
     * @param integer $websiteId
     * @return Enterprise_Reward_Model_Reward_Rate
     */
    public function fetch($customerGroupId, $websiteId) {
        $this->setData('original_website_id', $websiteId)
            ->setData('original_Customer_group_id', $customerGroupId);
        if ($customerGroupId && $websiteId) {
            $this->_getResource()->fetch($this, $customerGroupId, $websiteId);
        }
        return $this;
    }

    /**
     * Calculate currency amount of given points by rate
     *
     * @param integer $points
     * @return float
     */
    public function calculateToCurrency($points)
    {
        $amount = 0;
        if ($this->getId()) {
            $roundedPoints = (int)($points/$this->getPoints());
            if ($roundedPoints) {
                $amount = $this->getCurrencyAmount()*$roundedPoints;
            }
        }
        return (float)$amount;
    }

    /**
     * Calculate points of given amount by rate
     *
     * @param float $amount
     * @return integer
     */
    public function calculateToPoints($amount)
    {
        $points = 0;
        return $points;
    }

    /**
     * Prepare customer group value.
     * Set group 'all' if customer group is NULL
     *
     * @return Enterprise_Reward_Model_Reward_Rate
     */
    public function prepareCustomerGroupValue()
    {
        if (null === $this->getData('customer_group_id')) {
            $this->setData('customer_group_id', self::RATE_CUSTOMER_GROUP_ID_ALL);
        }
        return $this;
    }

    /**
     * Prepare and return rate as a text by given type
     *
     * @param integer $type
     * @return string
     */
    public function getExchangeRateAsText()
    {
        $label = '';
        $websiteId = $this->getOriginalWebsiteId();
        if ($websiteId === null) {
            $websiteId = $this->getWebsiteId();
        }
        if ($websiteId) {
            $currencyCode = Mage::app()->getWebsite($websiteId)->getBaseCurrencyCode();
        }
        switch($this->getDirection()) {
            case self::RATE_EXCHANGE_DIRECTION_TO_CURRENCY:
                if ($websiteId) {
                    $currencyAmount = Mage::app()->getLocale()->currency($currencyCode)
                        ->toCurrency($this->getCurrencyAmount());
                    $label = Mage::helper('enterprise_reward')->__('%d PTS is equal to %s', (int)$this->getPoints(), $currencyAmount);
                } else {
                    $label = Mage::helper('enterprise_reward')->__('%d PTS is equal to %s in Website Currency', (int)$this->getPoints(), number_format($this->getCurrencyAmount(), 4));
                }
                break;
            case self::RATE_EXCHANGE_DIRECTION_TO_POINTS:
                if ($websiteId) {
                    $currencyAmount = Mage::app()->getLocale()->currency($currencyCode)
                        ->toCurrency($this->getCurrencyAmount());
                    $label = Mage::helper('enterprise_reward')->__('%s is equal to %d PTS', $currencyAmount, (int)$this->getPoints());
                } else {
                    $label = Mage::helper('enterprise_reward')->__('%s in Website Currency is equal to %d PTS', number_format($this->getCurrencyAmount(), 4), (int)$this->getPoints());
                }
                break;
        }
        return $label;
    }

    /**
     * Retrieve option array of rate directions with labels
     *
     * @return array
     */
    public function getDirectionsOptionArray()
    {
        $optArray = array(
            self::RATE_EXCHANGE_DIRECTION_TO_CURRENCY => Mage::helper('enterprise_reward')->__('Points To Currency'),
            self::RATE_EXCHANGE_DIRECTION_TO_POINTS => Mage::helper('enterprise_reward')->__('Currency To Points')
        );
        return $optArray;
    }
}
