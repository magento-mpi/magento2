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
class Community2_Mage_Customer_Helper extends Core_Mage_Customer_Helper
{
    /**
     * Check if customer is present in customers grid
     *
     * @param array $userData
     *
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
