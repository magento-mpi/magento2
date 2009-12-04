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
    const REWARD_RATE_GROUP_ID_ALL         = 'all';
    const REWARD_EXCHANGE_RATE_TO_CURRENCY = 1;
    const REWARD_EXCHANGE_RATE_TO_POINTS   = 2;

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
        if ($this->getData('customer_group_id') == self::REWARD_RATE_GROUP_ID_ALL) {
            $this->setData('customer_group_id', null);
        }
        return $this;
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
     * Fetch rate by customer group and website
     *
     * @param integer $customerGroupId
     * @param integer $websiteId
     * @return Enterprise_Reward_Model_Reward_Rate
     */
    public function fetch($customerGroupId, $websiteId) {
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
        $roundedPoints = (int)($points/$this->getPointsCount());
        if ($roundedPoints) {
            $amount = $this->getPointsCurrencyValue()*$roundedPoints;
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
            $this->setData('customer_group_id', self::REWARD_RATE_GROUP_ID_ALL);
        }
        return $this;
    }

    /**
     * Prepare and return rate as a text by given type
     *
     * @param integer $type
     * @return string
     */
    public function getExchangeRateAsText($type = 1)
    {
        $label = '';
        switch($type) {
            case self::REWARD_EXCHANGE_RATE_TO_CURRENCY:
                $label = $this->getPointsCount().' is ' . $this->getPointsCurrencyValue() . 'in website currency';
                break;
            case self::REWARD_EXCHANGE_RATE_TO_POINTS:
                $label = $this->getCurrencyAmount().' is ' . $this->getCurrencyPointsValue();
                break;
        }
        return $label;
    }
}