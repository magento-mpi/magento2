<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
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
 * @category    tests
 * @package     selenium
 * @subpackage  tests
 * @author      Magento Core Team <core@magentocommerce.com>
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
/**
 * Add address tests.
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Helper class
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Enterprise2_Mage_Customer_Helper extends Core_Mage_Customer_Helper
{

    /**
     * Updating Customer Store Credit Balance
     *
     * @param array $storeCreditData Store credit Information
     * @return void
     */
    public function updateStoreCreditBalance(array $storeCreditData)
    {
         $this->fillTab($storeCreditData, 'store_credit');
         $this->saveForm('save_customer');
    }
    /**
     * Updating Customer Reward Points Balance
     *
     * @param array $rewardPointsData Store credit Information
     * @return void
     */
    public function updateRewardPointsBalance(array $rewardPointsData)
    {
        $this->fillTab($rewardPointsData, 'reward_points');
        $this->saveForm('save_customer');
    }
    /**
     * Get Current Customer Store Credit Balance
     *
     * @return string
     */
    public function getStoreCreditBalance()
    {
        $this->openTab('store_credit');
        return trim($this->getText($this->_getControlXpath('field', 'current_balance')));
    }
    /**
     * Get Current Customer Store Credit Balance
     *
     * @return string
     */
    public function getRewardPointsBalance()
    {
        $this->openTab('reward_points');
        return trim($this->getText($this->_getControlXpath('field', 'current_balance')));
    }
}