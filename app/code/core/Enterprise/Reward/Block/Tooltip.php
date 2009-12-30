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
 * Advertising Tooltip block to show different messages for gaining reward points
 *
 * @category    Enterprise
 * @package     Enterprise_Reward
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Reward_Block_Tooltip extends Mage_Core_Block_Template
{
    /**
     * Check whether tooltip is enabled
     *
     * @param string $code Unique code for each type of points rewards
     * @return bool
     */
    public function canShow($action)
    {
        return Mage::helper('enterprise_reward')->isEnabled()
            && $this->getRewardPoints($action) > 0
            && !$this->isLimitExceeded()
            && !$this->isActionLimitExceeded($action);
    }

    /**
     * Description goes here...
     *
     * @param none
     * @return void
     */
    public function getLandingPageUrl()
    {
        $pageIdentifier = Mage::getStoreConfig('enterprise_reward/general/landing_page');
        return Mage::getUrl('', array('_direct' => $pageIdentifier));
    }

    /**
     * Return points delta for each type of points rewards
     *
     * @param string $code Unique code for each type of points rewards
     * @return int
     */
    public function getRewardPoints($action)
    {
        if (!$this->hasData($action . 'reward_points')) {
            if ($this->_getAction($action)) {
                $result = $this->_getAction($action)->getPoints(Mage::app()->getWebsite()->getId());
            } else {
                $result = 0;
            }
            $this->setData($action . 'reward_points', $result);
        }
        return $this->_getData($action . 'reward_points');
    }

    /**
     * Return points delta for each type of points rewards
     *
     * @param string $code Unique code for each type of points rewards
     * @return int
     */
    public function getCurrencyAmount($action)
    {
        if (!$this->hasData($action . 'currency_amount')) {
            if ($this->isCustomerLoggedIn()) {
                /* @var $rate Enterprise_Reward_Model_Reward_Rate */
                $rate = Mage::getModel('enterprise_reward/reward_rate')->fetch(
                    $this->_getCustomer()->getGroupId(),
                    Mage::app()->getWebsite()->getId(),
                    Enterprise_Reward_Model_Reward_Rate::RATE_EXCHANGE_DIRECTION_TO_CURRENCY
                );
                if ($rate->getId()) {
                    $result = $rate->calculateToCurrency($this->getRewardPoints($action), false);
                } else {
                    $result = 0;
                }
            } else {
                $result = 0;
            }
            $this->setData($action . 'currency_amount', $result);
        }
        return $this->_getData($action . 'currency_amount');
    }

    /**
     * Description goes here...
     *
     * @param none
     * @return void
     */
    public function getFormattedAmount($currencyAmount)
    {
        return Mage::helper('core')->currency($currencyAmount);
    }

    /**
     * Description goes here...
     *
     * @param none
     * @return void
     */
    public function getCurrentBalance()
    {
        if ($this->isRewardExists()) {
            return $this->_getReward()->getPointsBalance();
        }
        return 0;
    }

    /**
     * Description goes here...
     *
     * @param none
     * @return void
     */
    protected function _getAction($action)
    {
        if (!$this->hasData($action . 'action_instance')) {
            $actionInstance = Mage::getModel('enterprise_reward/reward')->getActionInstance($action);
            $this->setData($action . 'action_instance', $actionInstance);
        }
        return $this->_getData($action . 'action_instance');
    }

    /**
     * Description goes here...
     *
     * @param none
     * @return void
     */
    public function isRewardExists()
    {
        if ($this->isCustomerLoggedIn() && $this->_getReward()->getId()) {
            return true;
        }
        return false;
    }

    /**
     * Description goes here...
     *
     * @param none
     * @return void
     */
    public function isLimitExceeded()
    {
        if ($this->isRewardExists()) {
            $max = (int)Mage::helper('enterprise_reward')->getGeneralConfig('max_points_balance');
            if ($max > 0) {
                return $this->_getReward()->getPointsBalance() >= $max;
            }
        }
        return false;
    }

    /**
     * Description goes here...
     *
     * @param none
     * @return void
     */
    public function isActionLimitExceeded($action)
    {
        if ($this->isRewardExists() && $this->_getAction($action)) {
            return $this->_getAction($action)
                ->isRewardLimitExceeded();
        }
        return false;
    }

    /**
     * Description goes here...
     *
     * @param none
     * @return void
     */
    public function getActionRewardLimit($action)
    {
        if ($this->_getAction($action)) {
            return $this->_getAction($action)
                ->getRewardLimit();
        }
        return 0;
    }

    /**
     * Description goes here...
     *
     * @param none
     * @return void
     */
    public function isCustomerLoggedIn()
    {
        return Mage::getSingleton('customer/session')->isLoggedIn();
    }

    /**
     * Getter for current customer
     *
     * @return Mage_Customer_Model_Customer
     */
    public function _getCustomer()
    {
        return Mage::getSingleton('customer/session')->getCustomer();
    }

    /**
     * Getter for current customer
     *
     * @return Mage_Customer_Model_Customer
     */
    public function _getReward()
    {
        if (!$this->hasData('reward')) {
            $reward = Mage::getModel('enterprise_reward/reward')
                ->setCustomer($this->_getCustomer())
                ->loadByCustomer();
            $this->setData('reward', $reward);
        }
        return $this->_getData('reward');
    }
}
