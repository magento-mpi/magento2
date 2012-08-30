<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     selenium
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
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
        if (!$continue) {
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
        if (!$continue) {
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
        $this->addParameter('webSiteName', 'No records found');
        if ($this->controlIsPresent('field', 'current_balance')) {
            return 'No records found.';
        }
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
        $this->addParameter('webSiteName', 'No records found');
        if ($this->controlIsPresent('field', 'balance_is_present')) {
            return 'No records found.';
        }
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

    /**
     * Verify that address is present.
     * PreConditions: Customer is opened on 'Addresses' tab.
     *
     * @param array $addressData
     *
     * @return int|mixed|string
     */
    public function isAddressPresent(array $addressData)
    {
        $xpath = $this->_getControlXpath('fieldset', 'list_customer_addresses') . '//li';
        $addressCount = $this->getXpathCount($xpath);
        for ($i = $addressCount; $i > 0; $i--) {
            $this->click($xpath . "[$i]");
            $id = $this->getValue($xpath . "[$i]/@id");
            $arrayId = explode('_', $id);
            $id = end($arrayId);
            $this->addParameter('address_number', $id);
            if ($this->verifyForm($addressData, 'addresses')) {
                $this->clearMessages();
                return $id;
            }
        }
        return 0;
    }

    /**
     * Two Step Password Reset
     *
     * @param array $emailData
     */
    public function frontForgotPassword($emailData)
    {
        $waitCondition = array($this->_getMessageXpath('general_success'), $this->_getMessageXpath('general_error'),
            $this->_getMessageXpath('general_validation'));
        $this->assertTrue($this->checkCurrentPage('forgot_customer_password'), $this->getParsedMessages());
        $this->fillFieldset($emailData, 'forgot_password');
        $this->clickButton('submit', false);
        $this->waitForElement($waitCondition);
        $this->validatePage();
    }
}