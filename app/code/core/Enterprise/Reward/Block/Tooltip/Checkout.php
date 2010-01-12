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
 * Checkout Tooltip block to show checkout cart message for gaining reward points
 *
 * @category    Enterprise
 * @package     Enterprise_Reward
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Reward_Block_Tooltip_Checkout extends Enterprise_Reward_Block_Tooltip
{
    /**
     * Check whether tooltip is enabled
     *
     * @param string $code Unique code for each type of points rewards
     * @return bool
     */
    public function canShow($action)
    {
        return (bool)(parent::canShow($action) && $this->isCustomerLoggedIn());
    }

    /**
     * Getter
     *
     * @return Mage_Sales_Model_Quote
     */
    public function getQuote()
    {
        return Mage::getSingleton('checkout/session')->getQuote();
    }

    /**
     * Return points delta, calculate basing on subtotal of quote
     *
     * @param string $code Unique code for each type of points rewards
     * @return int
     */
    public function getRewardPoints($action)
    {
        if (!$this->hasData($action . 'reward_points')) {
            /* @var $rate Enterprise_Reward_Model_Reward_Rate */
            $rate = Mage::getModel('enterprise_reward/reward_rate')->fetch(
                $this->getCustomer()->getGroupId(), Mage::app()->getStore()->getWebsiteId(),
                Enterprise_Reward_Model_Reward_Rate::RATE_EXCHANGE_DIRECTION_TO_POINTS);
            $result = $rate->calculateToPoints($this->getQuote()->getBaseSubtotal());
            $this->setData($action . 'reward_points', $result);
        }
        return $this->_getData($action . 'reward_points');
    }
}