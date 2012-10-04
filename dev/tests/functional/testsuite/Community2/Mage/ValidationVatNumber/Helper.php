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

    /**
     * Creates order
     *
     * @param array|string $orderData Array or string with name of dataset to load
     * @param bool $validate (If $validate == false - errors will be skipped while filling order data)
     * @param array|string $testData
     * @param array|string $userAddressData
     * @param array $messageType
     *
     * @return bool|string
     */
    public function createOrder($orderData, $testData, $userAddressData, $messageType, $validate = true)
    {
        $this->orderHelper()->doAdminCheckoutSteps($orderData, $validate);
        $this->pleaseWait();
        $this->validationVatMessages($testData, $userAddressData, $messageType);
        $this->orderHelper()->submitOrder();
    }

    /**
     * Clicking button "Validate VAT Number" and confirm popup message
     *
     * @param array|string $testData
     * @param array|string $userAddressData
     * @param string $messageType
     */
    public function validationVatMessages($testData, $userAddressData, $messageType)
    {
        if ($messageType == 'validIntraunionMessage') {
            $this->addParameter('newCustomerGroup', $testData['customerGroups']['group_valid_vat_intraunion']);
            $this->addParameter('currentCustomerGroup', $testData['customerGroups']['group_default']);
            $this->clickButtonAndConfirm('billing_validate_vat_number', 'valid_domestic_group_message', false);
            $this->pleaseWait();
            $this->verifyForm(array('customer_group' => $testData['customerGroups']['group_valid_vat_intraunion']));
        } elseif ($messageType == 'validDomesticMessage') {
            $this->addParameter('newCustomerGroup', $testData['customerGroups']['group_valid_vat_domestic']);
            $this->addParameter('currentCustomerGroup', $testData['customerGroups']['group_default']);
            $this->clickButtonAndConfirm('billing_validate_vat_number', 'valid_domestic_group_message', false);
            $this->pleaseWait();
            $this->verifyForm(array('customer_group' => $testData['customerGroups']['group_valid_vat_domestic']));
        } elseif ($messageType == 'invalidMessage') {
            $this->addParameter('vatNumber', $userAddressData['billing_vat_number']);
            $this->addParameter('newCustomerGroup', $testData['customerGroups']['group_invalid_vat']);
            $this->addParameter('currentCustomerGroup', $testData['customerGroups']['group_default']);
            $this->clickButtonAndConfirm('billing_validate_vat_number', 'invalid_vat_id_message', false);
            $this->pleaseWait();
            $this->verifyForm(array('customer_group' => $testData['customerGroups']['group_invalid_vat']));
        } else {
            $this->addVerificationMessage('Incorrect message type');
        }
    }

    /**
     * Submit form and confirm the confirmation popup with the specified message.
     *
     * @param string $buttonName Name of a button from UIMap
     * @param string $message Confirmation message id from UIMap
     * @param bool $willChangePage Triggers page reloading. If clicking the control doesn't result<br>
     * in page reloading, should be false (by default = true).
     *
     * @return bool
     */
    public function clickButtonAndConfirm($buttonName, $message, $willChangePage = true)
    {
        return $this->clickControlAndConfirm('button', $buttonName, $message, $willChangePage);
    }

    /**
     * Clicks a control with the specified name and type
     * and confirms the confirmation popup with the specified message.
     *
     * @param string $controlType Type of control (e.g. button|link)
     * @param string $controlName Name of a control from UIMap
     * @param string $message Confirmation message
     * @param bool $willChangePage Triggers page reloading. If clicking the control doesn't result<br>
     * in page reloading, should be false (by default = true).
     *
     * @return bool
     */
    public function clickControlAndConfirm($controlType, $controlName, $message, $willChangePage = true)
    {
        $buttonXpath = $this->_getControlXpath($controlType, $controlName);
        if ($this->isElementPresent($buttonXpath)) {
            $confirmation = $this->_getMessageXpath($message);
            $this->chooseCancelOnNextConfirmation();
            $this->click($buttonXpath);
            $this->waitForAjax();
            if ($this->isConfirmationPresent()) {
                $text = $this->getConfirmation();
                $confirmation = trim($confirmation);
                $text = trim($text);
                $this->assertSame($text, $confirmation);
                if ($text == $confirmation) {
                    $this->chooseOkOnNextConfirmation();
                    $this->click($buttonXpath);
                    $this->waitForAjax();
                    $this->getConfirmation();
                    if ($willChangePage) {
                        $this->waitForPageToLoad($this->_browserTimeoutPeriod);
                        $this->validatePage();
                    }
                    return true;
                } else {
                    $this->addVerificationMessage("The confirmation text incorrect: {$text}");
                }
            } else {
                $this->addVerificationMessage('The confirmation does not appear');
                if ($willChangePage) {
                    $this->waitForPageToLoad($this->_browserTimeoutPeriod);
                    $this->validatePage();
                }
                return true;
            }
        } else {
            $this->addVerificationMessage("There is no way to click on control(There is no '$controlName' control)");
        }

        return false;
    }
}
