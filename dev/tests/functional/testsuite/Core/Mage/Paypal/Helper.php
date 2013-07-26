<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Paypal
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
class Core_Mage_Paypal_Helper extends Mage_Selenium_AbstractHelper
{
    public static $monthMap = array('1' => '01 - January', '2' => '02 - February', '3' => '03 - March',
        '4' => '04 - April', '5' => '05 - May', '6' => '06 - June', '7' => '07 - July',
        '8' => '08 - August', '9' => '09 - September', '10' => '10 - October',
        '11' => '11 - November', '12' => '12 - December'
    );

    /**
     * Verify errors after order submitting. Skip tests if error from Paypal
     */
    public function verifyMagentoPayPalErrors()
    {
        $paypalErrors = array(
            'PayPal gateway rejected the request',
            'PayPal gateway has rejected request',
            'We can\'t contact the PayPal gateway.',
            'Gateway error: This transaction cannot be accepted.',
            'Gateway error: Unable to read response, or response is empty',
            'Please verify the card with the issuer bank before placing the order.',
            'Something went wrong processing your order. Please try again later.'
        );
        $submitErrors = $this->getMessagesOnPage('error,validation,verification');
        foreach ($submitErrors as $error) {
            foreach ($paypalErrors as $paypalError) {
                if (strpos($error, $paypalError) !== false) {
                    $this->skipTestWithScreenshot(self::messagesToString($this->getMessagesOnPage()));
                }
            }
        }
    }

    ################################################################################
    #                                                                              #
    #                                   PayPal Developer                           #
    #                                                                              #
    ################################################################################
    /**
     * Log into Paypal developer's site
     */
    public function paypalDeveloperLogin()
    {
        try {
            $this->goToArea('paypal_developer', 'paypal_developer_home');
        } catch (Exception $e) {
            $this->skipTestWithScreenshot($e->getMessage());
        }
        if ($this->controlIsPresent('button', 'login_with_paypal')) {
            $loginData = array(
                'login_email' => $this->getConfigHelper()->getDefaultLogin(),
                'login_password' => $this->getConfigHelper()->getDefaultPassword()
            );
            $this->clickButton('login_with_paypal', false);
            $this->selectLastWindow();
            $this->fillFieldset($loginData, 'login_form');
            $this->getControlElement('button', 'login')->click();
            $this->waitForElementVisible(array(
                $this->_getControlXpath('pageelement', 'loadingHolder'),
                $this->_getMessageXpath('general_validation')
            ));
            $error = $this->errorMessage();
            $validation = $this->validationMessage();
            $this->assertFalse($error['success'] || $validation['success'], $this->getMessagesOnPage());
            $this->waitForWindowToClose();
            $this->waitForControlVisible('button', 'logout');
        }
        $result = $this->errorMessage();
        $this->assertFalse($result['success'], $this->getMessagesOnPage());
    }

    /**
     * Creates preconfigured Paypal Sandbox account
     *
     * @param string|array $parameters
     *
     * @return array
     */
    public function createPreconfiguredAccount($parameters)
    {
        if (is_string($parameters)) {
            $parameters = $this->loadDataSet('Paypal', $parameters);
        }
        $this->navigate('paypal_developer_create_account');
        $this->fillFieldset($parameters, 'create_test_account_form');
        $this->clickButton('create_account');
        $this->validatePage();
        $result = $this->successMessage('success_created_account');
        if (!$result['success']) {
            $this->takeScreenshot(time() . '-error in paypal-' . $this->getTestId());
            $parameters['login_email'] = $this->generate('email', 20, 'valid');
            $this->createPreconfiguredAccount($parameters);
        }

        return $this->getPaypalSandboxAccountInfo($parameters);
    }

    /**
     * Gets the email for newly created sandbox account
     *
     * @param array $parameters
     *
     * @return array
     */
    public function getPaypalSandboxAccountInfo(array $parameters)
    {
        $this->openAccountDetailsTab($parameters['login_email'], 'funding_tab');
        $data['email'] = $parameters['login_email'];
        //Get Credit card data
        $data['credit_card']['card_type'] = $parameters['add_credit_card'];
        $this->addParameter('propertyName', 'Credit card number:');
        $data['credit_card']['card_number'] =
            $this->getControlAttribute(self::FIELD_TYPE_PAGEELEMENT, 'property', 'text');
        $this->addParameter('propertyName', 'Expiration date:');
        list($expMonth, $expYear) =
            explode('/', $this->getControlAttribute(self::FIELD_TYPE_PAGEELEMENT, 'property', 'text'));
        $data['credit_card']['expiration_month'] = self::$monthMap[trim($expMonth)];
        $data['credit_card']['expiration_year'] = $expYear;
        $data['credit_card'] = array_map('trim', $data['credit_card']);

        return $data;
    }

    /**
     * Gets API Credentials for account
     *
     * @param string $email
     *
     * @return array
     */
    public function getApiCredentials($email)
    {
        $this->openAccountDetailsTab($email, 'api_credentials_tab');
        $apiCredentials = array();

        $keys = array('api_username', 'api_password', 'api_signature');
        foreach ($keys as $value) {
            $apiCredentials[$value] = $this->getControlAttribute(self::FIELD_TYPE_PAGEELEMENT, $value, 'text');
        }
        $apiCredentials['email_associated_with_paypal_merchant_account'] = $email;
        return $apiCredentials;
    }

    /**
     * Open tab on account details popup
     *
     * @param string $email Account email address
     * @param string $tabName Link to open tab
     */
    private function openAccountDetailsTab($email, $tabName)
    {
        if ('paypal_developer_sandbox_accounts' != $this->getCurrentPage()) {
            $this->navigate('paypal_developer_sandbox_accounts');
        }
        if (!$this->controlIsVisible(self::UIMAP_TYPE_FIELDSET, 'account_details_popup')) {
            $this->addParameter('accountEmail', $email);
            $this->clickControl(self::FIELD_TYPE_LINK, 'account_details', false);
            $this->waitForControlVisible(self::FIELD_TYPE_LINK, 'account_profile');
            $this->clickControl(self::FIELD_TYPE_LINK, 'account_profile', false);
            $this->waitForControlVisible(self::UIMAP_TYPE_FIELDSET, 'account_details_popup');
        }
        $this->clickControl(self::FIELD_TYPE_LINK, $tabName, false);
        $this->addParameter('tabTitle', $this->getControlAttribute(self::FIELD_TYPE_LINK, $tabName, 'text'));
        $this->waitForControl(self::FIELD_TYPE_PAGEELEMENT, 'active_tab');
    }

    /**
     * Deletes all accounts at PayPal sandbox
     */
    public function deleteAllAccounts()
    {
        //Show 50 accounts per page
        $this->setUrlPostfix('?numAccounts=50');
        $this->navigate('paypal_developer_sandbox_accounts');
        $this->setUrlPostfix(null);
        $this->fillCheckbox('select_all_accounts', 'Yes');
        if ($this->controlIsVisible('button', 'delete_account')) {
            $this->clickButton('delete_account', false);
            $this->waitForControlVisible(self::UIMAP_TYPE_FIELDSET, 'delete_account_popup');
            $this->clickButton('delete');
        }
    }

    /**
     * Deletes account at PayPal sandbox
     *
     * @param string $email
     */
    public function deleteAccount($email)
    {
        //Show 50 accounts per page
        $this->setUrlPostfix('?numAccounts=50');
        $this->navigate('paypal_developer_sandbox_accounts');
        $this->setUrlPostfix(null);
        $this->addParameter('accountEmail', $email);
        if ($this->controlIsVisible('pageelement', 'account_line')
            && $this->controlIsEditable('checkbox', 'select_account')
        ) {
            $this->fillCheckbox('select_account', 'Yes');
            $this->clickButton('delete_account', false);
            $this->waitForControlVisible(self::UIMAP_TYPE_FIELDSET, 'delete_account_popup');
            $this->clickButton('delete');
        }
    }

    /**
     * Create Buyers Accounts on PayPal sandbox
     *
     * @param array|string $cards mastercard, visa, discover, amex
     *
     * @return array $accounts
     * @test
     */
    public function createBuyerAccounts($cards)
    {
        if (is_string($cards)) {
            $cards = explode(',', $cards);
            $cards = array_map('trim', $cards);
        }
        $accounts = array();
        foreach ($cards as $card) {
            $info = $this->loadDataSet('Paypal', 'paypal_sandbox_new_buyer_account_' . $card);
            $accounts[$card] = $this->createPreconfiguredAccount($info);
            if ($card != 'amex') {
                $accounts[$card]['credit_card']['card_verification_number'] = '111';
            } else {
                $accounts[$card]['credit_card']['card_verification_number'] = '1234';
            }
        }
        return $accounts;
    }
}
