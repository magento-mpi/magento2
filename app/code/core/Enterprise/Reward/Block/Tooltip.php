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
    public function canShow($code)
    {
        return Mage::helper('enterprise_reward')->isEnabled() && $this->getGainedRewardPoints($code) > 0;
    }

    /**
     * Return points delta for each type of points rewards
     *
     * @param string $code Unique code for each type of points rewards
     * @return int
     */
    public function getGainedRewardPoints($code)
    {
        return $this->getDataSetDefault('reward_points', (int)Mage::getStoreConfig('enterprise_reward/points/' . $code));
    }
}
