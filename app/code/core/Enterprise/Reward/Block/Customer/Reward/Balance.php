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
 * Customer account reward points balance block
 *
 * @category    Enterprise
 * @package     Enterprise_Reward
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Reward_Block_Customer_Reward_Balance extends Mage_Core_Block_Template
{
    /**
     * Getter
     *
     * @return Mage_Customer_Model_Customer
     */
    public function getCustomer()
    {
        return Mage::getSingleton('customer/session')->getCustomer();
    }

    /**
     * Getter
     *
     * @return Enterprise_Reward_Model_Reward
     */
    public function getReward()
    {
        /* @var $reward Enterprise_Reward_Model_Reward */

        if (!$this->getData('reward') && ($this->getCustomer() && $this->getCustomer()->getId())) {
            $reward = Mage::getModel('enterprise_reward/reward')
                ->loadByCustomer($this->getCustomer()->getId());
            $this->setData('reward', $reward);
        }
        return $this->getData('reward');
    }

    /**
     * Getter
     *
     * @return integer
     */
    public function getPoints()
    {
        return $this->getReward()->getPoints();
    }
}