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
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Helper class
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Paypal_Helper extends Mage_Selenium_TestCase
{
    public static $monthMap = array('1'  => '01 - January',
                                    '2'  => '02 - February',
                                    '3'  => '03 - March',
                                    '4'  => '04 - April',
                                    '5'  => '05 - May',
                                    '6'  => '06 - June',
                                    '7'  => '07 - July',
                                    '8'  => '08 - August',
                                    '9'  => '09 - September',
                                    '10' => '10 - October',
                                    '11' => '11 - November',
                                    '12' => '12 - December');
    /**
     * Log into Paypal developer's site
     *
     * @param string|array $parameters
     */
    public function paypalDeveloperLogin($parameters)
    {
        if (is_string($parameters)) {
            $parameters = $this->loadData($parameters);
        }
        $xpath = $this->getUimapPage('paypal-developer', 'home')->findButton('button_login');
        if ($this->isElementPresent($xpath)) {
            $this->validatePage();
            $this->fillForm($parameters);
            $this->clickControl('button', 'button_login');
        }
    }

    /**
     * Creates Paypal Sandbox account
     *
     * @param string|array $parameters
     */
    public function createPaypalSandboxAccount($parameters)
    {
        if (is_string($parameters)) {
            $parameters = $this->loadData($parameters);
        }
        $parameters = $this->arrayEmptyClear($parameters);
        $this->fillForm($parameters);
        $xpath = $this->_getControlXpath('button', 'create_account');
        while ($this->isElementPresent($xpath)) {
            $this->click($xpath);
            $notLoaded = true;
            $retries = 0;
            while ($notLoaded) {
                try {
                    $retries++;
                    $this->waitForPageToLoad($this->_browserTimeoutPeriod);
                    $notLoaded = false;
                } catch (PHPUnit_Framework_Exception $e) {
                    if ($retries == 10) {
                        throw $e;
                    }
                }
            }
        }
    }

    /**
     * Gets API Credentials for account
     *
     * @param string $email
     * @return array
     */
    public function getApiCredentials($email)
    {
        $this->addParameter('emailPart', $email);
        $pageelements = $this->getCurrentUimapPage()->getAllPageelements();
        $info = array();
        foreach ($pageelements as $key => $value) {
            if ($this->isElementPresent($value)) {
                $info[$key] = $this->getText($value);
            } else {
                $this->fail('Could not find element: ' . $key);
            }

        }
        return $info;
    }

    /**
     * Gets the email for newly created sandbox account
     *
     * @param $parameters
     * @return string
     */
    public function getPaypalSandboxAccountInfo($parameters)
    {
        $this->addParameter('emailPart', $parameters['login_email']);
        $info = array();
        $info['credit_card']['card_type'] = $parameters['add_credit_card'];
        $pageelements = $this->getCurrentUimapPage()->getAllPageelements();
        foreach ($pageelements as $key => $value) {
            switch ($key) {
                case 'credit_card':
                    if (!$this->isElementPresent($value)) {
                        $this->fail('Could not find element: ' . $value);
                    } else {
                        $text = $this->getText($value);
                        $nodes = explode("\n", $text);
                        foreach ($nodes as $line) {
                            $exline = explode(' ', $line);
                            foreach ($exline as $val) {
                                if (preg_match('/([0-9])/', $val)) {
                                    if (preg_match('/Exp Date/', $line)) {
                                        $expDate = explode('/', $val);
                                        $info[$key]['expiration_month'] = self::$monthMap[$expDate[0]];
                                        $info[$key]['expiration_year'] = $expDate[1];
                                    } else {
                                        $info[$key]['card_number'] = $val;
                                    }
                                }
                            }
                        }
                    }
                    break;
                case 'email':
                    if (!$this->isElementPresent($value)) {
                        $this->fail('Could not find element: ' . $value);
                    } else {
                        $info['email'] = $this->getText($value);
                    }
                default:
                    break;
            }
        }
        return $info;
    }

    /**
     * Deletes all accounts at PayPal sandbox
     */
    public function deleteAllAccounts()
    {
        $this->navigate('test_accounts');
        while ($this->controlIsPresent('button', 'delete')) {
            $this->chooseOkOnNextConfirmation();
            $this->clickButton('delete', false);
            try {
                $this->waitForPageToLoad($this->_browserTimeoutPeriod);
            } catch (PHPUnit_Framework_Exception $e) {
            }
            $this->navigate('test_accounts');
        }
    }

    /**
     * Deletes account at PayPal sandbox
     *
     * @param string $email
     */
    public function deleteAccount($email)
    {
        $this->navigate('test_accounts');
        $this->addParameter('emailPart', $email);
        if ($this->controlIsPresent('checkbox', 'account')) {
            $this->fillForm(array('account' => 'Yes'));
            $this->navigate('test_accounts');
            if ($this->controlIsPresent('button', 'delete')) {
                $this->chooseOkOnNextConfirmation();
                $this->clickButton('delete', false);
                try {
                    $this->waitForPageToLoad($this->_browserTimeoutPeriod);
                } catch (PHPUnit_Framework_Exception $e) {
                }
                $this->navigate('test_accounts');
            }
        }

    }

    /**
     * Create Buyers Accounts on PayPal sandbox
     *
     * @param array|string $cards mastercard, visa, discover, amex
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
            $this->navigate('create_preconfigured_account');
            $info = $this->loadData('paypal_sandbox_new_buyer_account_' . $card);
            $this->createPaypalSandboxAccount($info);
            $this->navigate('test_accounts');
            $accounts[$card] = $this->getPaypalSandboxAccountInfo($info);
            if ($card != 'amex') {
                $accounts[$card]['credit_card']['card_verification_number'] = '111';
            } else {
                $accounts[$card]['credit_card']['card_verification_number'] = '1234';
            }
        }
        return $accounts;
    }

    /**
     * Create Pro Merchant Account on PayPal sandbox
     *
     * @param string|array $accountData
     * @return array
     * @test
     */
    public function createPayPalProAccount($accountData)
    {
        if (is_string($accountData)) {
            $accountData = $this->loadData($accountData);
        }
        $accountData = $this->arrayEmptyClear($accountData);
        $this->navigate('create_preconfigured_account');
        $this->createPaypalSandboxAccount($accountData);
        $this->navigate('test_accounts');
        $info = $this->getPaypalSandboxAccountInfo($accountData);
        $this->navigate('api_credentials');

        return $this->getApiCredentials($info['email']);
    }

    /**
     * Login using sandbox account
     * Function has not been verified and is not used right now
     *
     * @param $parameters
     */
    public function paypalSandboxLogin($parameters)
    {
        if (is_string($parameters)) {
            $parameters = $this->loadData($parameters);
        }
        $xpath = $this->getUimapPage('paypal-sandbox', 'paypal_sandbox')->findButton('button_login');
        if ($this->isElementPresent($xpath)) {
            $this->addParameter('pageTitle', $parameters['page_title']);
            $this->validatePage();
            $this->fillForm($parameters['credentials']);
            $this->clickControl('button', 'button_login');
        }
    }

    /**
     * Configure sandbox account
     * Function has not been verified and is not used right now
     *
     * @param $parameters
     */
    public function paypalSandboxConfigure($parameters)
    {
        if (is_string($parameters)) {
            $parameters = $this->loadData($parameters);
        }
        $this->addParameter('pageTitle', $parameters['page_title']);
        $this->validatePage();
        $this->fillForm($parameters['credentials']);
        $this->clickControl('button', 'button_login');
        $this->clickControl('button', 'button_iagree');
    }

    /**
     * Pays the order using paypal sandbox account
     * Function has not been verified and is not used right now
     *
     * @param $parameters
     */
    public function paypalPayOrder($parameters)
    {
        if (is_string($parameters)) {
            $parameters = $this->loadData($parameters);
        }
        $xpath = $this->getUimapPage('paypal-sandbox', 'paypal_sandbox')->findButton('button_login');
        if (!$this->isElementPresent($xpath)) {
            $this->addParameter('pageTitle', $parameters['page_title_pay_with']);
            $this->validatePage();
            $this->addParameter('pageTitle', $parameters['page_title']);
            $this->clickControl('link', 'have_paypal_account');
        } else {
            $this->addParameter('pageTitle', $parameters['page_title']);
            $this->validatePage();
        }
        $this->fillForm($parameters['credentials']);
        $this->addParameter('pageTitle', $parameters['page_title_review_info']);
        $this->clickControl('button', 'button_login');
        $this->clickControl('button', 'button_continue');
    }
}