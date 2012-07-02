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
     * @param boolean $continue Press Save And Continue instead of Save
     * @return void
     */
    public function updateStoreCreditBalance(array $storeCreditData, $continue = false)
    {
        $this->fillTab($storeCreditData, 'store_credit');
        $this->clearMessages();
        if (!$continue){
            $this->saveForm('save_customer');
        }
    }
    /**
     * Updating Customer Reward Points Balance
     *
     * @param array $rewardPointsData Store credit Information
     * @param boolean $continue Press Save And Continue instead of Save
     * @return void
     */
    public function updateRewardPointsBalance(array $rewardPointsData, $continue = false)
    {
        $this->fillTab($rewardPointsData, 'reward_points');
        $this->clearMessages();
        if (!$continue){
            $this->saveForm('save_customer');
        }
    }
    /**
     * Get Current Customer Store Credit Balance
     *
     * @param string $webSiteName
     * @return string
     */
    public function getStoreCreditBalance($webSiteName = '')
    {
        $this->openTab('store_credit');
        $this->addParameter('webSiteName', $webSiteName);
        return trim($this->getText($this->_getControlXpath('field', 'current_balance')));
    }
    /**
     * Get Current Customer Store Credit Balance
     *
     * @param string $webSiteName
     * @return string
     */
    public function getRewardPointsBalance($webSiteName = '')
    {
        $this->openTab('reward_points');
        $this->addParameter('webSiteName', $webSiteName);
        return trim($this->getText($this->_getControlXpath('field', 'current_balance')));
    }
    /**
     * Check if customer is present in customers grid
     *
     * @param array $userData
     * @return bool
     */
    public function isCustomerPresentInGrid($userData)
    {
        $data = array('email' => $userData['email']);
        $this->_prepareDataForSearch($data);
        $xpathTR = $this->search($data, 'customers_grid');
        if (!is_null($xpathTR)) {
            return true;
        } else {
            return false;
        }
    }
}