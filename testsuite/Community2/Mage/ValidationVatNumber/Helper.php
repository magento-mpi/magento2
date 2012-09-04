<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_ValidationVatNumber
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
class Community2_Mage_ValidationVatNumber_Helper extends Mage_Selenium_TestCase
{
    /**
     * Verifying Customer  Group on backend
     *
     * @param array $userData
     * @param string $userDataParam
     */
    public function verifyCustomerGroup($userDataParam, $userData)
    {
        $this->loginAdminUser();
        $this->navigate('manage_customers');
        $this->addParameter('customer_first_last_name', $userDataParam);
        $this->customerHelper()->openCustomer(array('email' => $userData['email']));
        $this->openTab('account_information');
    }
}

