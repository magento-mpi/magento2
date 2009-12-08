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
        $this->loadByCustomer($this->getCustomerId());
        $this->_preparePointsBalance();
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
            ->prepareFromObject($this)
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
        return $this->getData('customer');
    }

    /**
     * Getter.
     * If website id not set, get it from customer
     *
     * @return integer
     */
    public function getWebsiteId()
    {
        if (!$this->getData('website_id') && $this->getCustomer()) {
            $this->setData('website_id', $this->getCustomer()->getWebsiteId());
        }
        return $this->getData('website_id');
    }

    /**
     * Getter.
     * Recalculate currency amount if need.
     *
     * @return float
     */
    public function getCurrencyAmount()
    {
        if ($this->getData('currency_amount') === null && $this->getId()) {
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
     * Initialize and fetch reward rate for customer group and website
     *
     * @return Enterprise_Reward_Model_Reward_Rate
     */
    public function getRate()
    {
        if (!$this->getData('rate') && $this->getCustomer()) {
            $rate = Mage::getModel('enterprise_reward/reward_rate')
                ->fetch($this->getCustomer()->getGroupId(), $this->getWebsiteId());
            $this->setData('rate', $rate);
        }
        return $this->getData('rate');
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
        if ($points && $this->getRate()) {
            $ammount = $this->getRate()->calculateToCurrency($points);
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
