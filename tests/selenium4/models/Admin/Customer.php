<?php

/**
 * Admin customer framework model
 *
 * @author Magento Inc.
 */
class Model_Admin_Customer extends Model_Admin {

    /**
     * Loading configuration data for the testCase
     */
    public function loadConfigData()
    {
        parent::loadConfigData();
        $this->Data = array();
        $this->aData = array();
        $this->customerId = '1'; //Core::getEnvConfig('backend/customer/id');
    }

    /**
     * creating Product Tax Class
     *
     * @param array $params May contain the following params:
     * product_tax_class_name
     *
     */
    public function addNewCustomer($params, $param, $parametr)
    {
        $this->printDebug('addNewCustomer() started...');
        $Data = $params ? $params : $this->Data;
        $aData = $param ? $param : $this->aData;
        $VerifyData = $parametr ? $parametr : $this->VerifyData;

        $this->setUiNamespace('admin/pages/customers/manage_customers/');
        $this->clickAndWait($this->getUiElement("/admin/topmenu/customer/managecustomers"));
        if ($this->isCustomerPresent($aData['CustID'])) {
            $this->printInfo('Customer is present');
            if ($this->isCustomerAddressPresent($aData['First_name'], $aData['CustID'])) {
                $this->printInfo('Customer has address');
                if (!$this->verifyAddress($params, $param, true, true, $parametr)) {
                    if ($this->addAddress($params, $param, $aData['is billing'], $aData['is shipping'])) {
                        $this->printInfo('One more address added to address book');
                    } else {
                        $this->printInfo('shit the brick');
                    }
                } else {
                    $this->printInfo('Customer has such address');
                }
            } else {
                $this->printInfo('This customer have no addresses in address book');
                $this->addAddress($params, $param, $aData['is billing'], $aData['is shipping']);
                $this->printInfo('Now he has one=)');
            }
        } else {
            $this->printInfo('There is no customer with such ID');
            if ($this->doAddNewCustomer($param)) {
                $this->addAddress($params, $param, $aData['is billing'], $aData['is shipping']);
                $this->printInfo('Customer added');
            } else {
                $this->printInfo('SHITSHITSHIT!!!');
            }
        }
    }

    /**
     *
     */
    public function doAddNewCustomer($param)
    {
        $aData = $param ? $param : $this->aData;

        $this->clickAndWait($this->getUiElement("/admin/pages/customers/manage_customers/buttons/add_new_customer"));
        $this->doEditCustomerAccountInfo($param);

        $this->setUiNamespace('admin/pages/customers/manage_customers');
        return true;
    }

    /**
     *
     */
    public function doEditCustomerAccountInfo($param)
    {
        $aData = $param ? $param : $this->aData;
        if ($this->isElementPresent($this->getUiElement("edit_customer_page/tabs/account_info") . "[not (contains(@class,'active'))]")) {
            $this->click($this->getUiElement("edit_customer_page/tabs/account_info"));
        }

        //fill all fields on account info tab
        $this->type($this->getUiElement("edit_customer_page/inputs/first_name"), $aData['First_name']);
        $this->type($this->getUiElement("edit_customer_page/inputs/last_name"), $aData['Last name']);
        $this->type($this->getUiElement("edit_customer_page/inputs/email"), $aData['Email']);
        $this->type($this->getUiElement("edit_customer_page/inputs/password"), $aData['password']);
        //select all selectors on account info tab
        $this->setUiNamespace('admin/pages/customers/manage_customers/edit_customer_page');
        $this->checkAndSelectField($param, 'associate_to_website', NULL);
        $this->checkAndSelectField($param, 'customer_group', NULL);
    }

    /**
     *
     */
    public function editingCustomer($param)
    {
        $this->printDebug('doEditCustomer() started...');
        $Data = $params ? $params : $this->Data;
        $aData = $param ? $param : $this->aData;
        $VerifyData = $parametr ? $parametr : $this->VerifyData;

        $this->setUiNamespace('admin/pages/customers/manage_customers/');
        $this->clickAndWait($this->getUiElement("/admin/topmenu/customer/managecustomers"));
        if ($this->isCustomerPresent($aData['CustID'])) {
            $this->printInfo('Customer is present');
            if ($this->isCustomerAddressPresent($aData['First_name'], $aData['CustID'])) {
                $this->doEditCustomerAccountInfo($parametr);
                $this->doAddAddress($params, $param, $isBilling, $isShipping);
                $this->editStoreCredit($params);
                $this->editRewardPoints($params);
            } else {
                $this->printInfo('This customer have no addresses in address book');
                $this->addAddress($params, $param, true, true);
                $this->printInfo('Now he has one=)');
            }
        } else {
            $this->printInfo("There is no such customer");
        }
    }

    /**
     * Open customer for the editing
     * @param $CustID - contains reurn
     * @returns true on success
     *
     */
    public function doOpenCustomer($CustID /* = null */)
    {
        //$this->click($this->getUiElement("admin/topmenu/customer/managecustomers"));
        //$this->waitForPageToLoad("90000");
        //$this->type("filter_entity_id_from", $CustID);
        //$this->type("filter_entity_id_to", $CustID);
        //$this->click($this->getUiElement("admin/customer/button/search"));
        //$this->pleaseWait();
        $this->printInfo("doOpenCustomer() started");
        if ($this->waitForElement($this->getUiElement("elements/customer_container_contains", $CustID), 30)) {
            $this->click($this->getUiElement("elements/customer_container_contains", $CustID));
            $this->waitForPageToLoad("90000");
            return true;
        } else {
            $this->setVerificationErrors("Customer " . $CustID . " could not be loaded");
            return false;
        }
    }

    /**
     * Determine existing of customer with ID = $CustID
     * @param $CustID - customer ID
     * @returns true if exists
     *
     */
    public function isCustomerPresent($CustID)
    {
        //$this->click($this->getUiElement("admin/topmenu/customer/managecustomers"));
        //$this->waitForPageToLoad("90000");
        $this->type("filter_entity_id_from", $CustID);
        $this->type("filter_entity_id_to", $CustID);
        $this->click($this->getUiElement("buttons/search"));

        $this->pleaseWait();
        sleep(1);

        return $this->isElementPresent($this->getUiElement("elements/customer_container_contains", $CustID));
    }

    /**
     * Determine existing address with FirstName containing $FirstName in customer with ID = $CustID
     * @param $CustID - customer ID
     * @param $FirstName - part of "First name" field
     * @returns true such sddress exists
     *
     */
    public function isCustomerAddressPresent($FirstName, $CustID)
    {
        $this->printInfo("isCustomerAddressPresent is started");
        if ($this->doOpenCustomer($CustID)) {
            // Open Address Tab
            $this->click($this->getUiElement("edit_customer_page/tabs/addresses_tab"));
            sleep(1);
            $this->printDebug('lalala');
            return $this->isElementPresent($this->getUiElement("edit_customer_page/elements/search_in_address_list", $FirstName));
        } else {
            return false;
        }
    }

    /**
     * Delete all addresses of customer with ID=$CustID
     * @param $CustID - customer ID
     *
     */
    public function delAllAddresses($params)
    {
        $Data = $params ? $params : $this->Data;
        $this->setUiNamespace('admin/pages/customers/manage_customers/');
        $this->clickAndWait($this->getUiElement("/admin/topmenu/customer/managecustomers"));
        if ($this->isCustomerPresent($Data['CustID'])) {
            $this->printInfo('Customer is present');
            if ($this->doOpenCustomer($Data['CustID'])) {
                $this->click($this->getUiElement("edit_customer_page/tabs/addresses_tab"));
                sleep(5);
                // Remove All Addresses
                //while ($this->isElementPresent($this->getUiElement("edit_customer_page/buttons/delete_address"))) {
                //$this->chooseCancelOnNextConfirmation();
                //$this->click($this->getUiElement("edit_customer_page/buttons/delete_address"));
                //if ($this->isConfirmationPresent()) {
                //    $text = $this->getConfirmation();
                //$this->chooseOkOnNextConfirmation();
                //$this->click($this->getUiElement("edit_customer_page/buttons/delete_address"));
                //if ($text == 'Are you sure you want to delete this address?') {
                //$this->chooseOkOnNextConfirmation();
                sleep(5);
                $this->click($this->getUiElement("edit_customer_page/buttons/delete_address"));
                sleep(5);
                $this->printInfo("shit the BRICKSSSS");
                sleep(5);


                //$this->chooseOkOnNextConfirmation();
                //$shell->SendKeys("{ENTER}");
                //$this->chooseOkOnNextConfirmation();
                //} else {
                //$this->printInfo('The confirmation text incorrect: ' . $text);
                //$result = FALSE;
                //}
                //} else {
                //$this->printInfo('The confirmation does not appear');
                //}
                //$this->getConfirmation();
                //if ($this->assertConfirmationPresent('Are you sure you want to delete this address?')) {
                //} else {
                //    $this->printInfo('An error was accured during deleting process');
                //}
                //}
                sleep(10);
                //$this->chooseOkOnNextConfirmation();
                $this->click($this->getUiElement("edit_customer_page/buttons/save_customer"));
                $this->keyPressNative("\032");
                sleep(10);
                //$this->saveCustomer();
            }
        }
    }

    /* public function saveCustomer()
      {
      $this->click($this->getUiElement("//button[span='Save Customer']"));
      // check for error message
      if ($this->waitForElement($this->getUiElement('/admin/global/messages/error1'), 20)) {
      $etext = $this->getText($this->getUiElement('/admin/global/messages/error1'));
      $this->setVerificationErrors($etext);
      }  elseif (!$this->verifyPageAndGetErrors()) {
      // Check for success message
      if ($this->waitForElement($this->getUiElement('/admin/messages/success'), 60)) {
      $etext = $this->getText($this->getUiElement('/admin/messages/success'));
      $this->printInfo($etext);
      $result = true;
      } else {
      $this->setVerificationErrors('No success message');
      }
      }
      return $result;
      } */

    /**
     * Delete address with FirstName containing $FirstName in customer with ID = $CustID
     * @param $CustID - customer ID
     * @param $FirstName - part of "First name" field*
     * @return false if customer doesnot exisrs
     */
    public function delAddresses($FirstName, $CustID = null)
    {
        if (null === $CustID) {
            $CustID = $this->customerId;
        }
        if ($this->doOpenCustomer($CustID)) {
            // Remove Test Addresses
            while ($this->isElementPresent($this->getUiElement("admin/customer/address/managepanel") . "//ul[contains(@id,'address_list')]//li[contains(address,'" . $FirstName . "')]//img[@alt='Remove address']")) {
                $this->click($this->getUiElement("admin/customer/address/managepanel") . "//ul[contains(@id,'address_list')]//li[contains(address,'" . $FirstName . "')]//img[@alt='Remove address']");
                $this->getConfirmation();
            };
            $this->doAdminSaveCustomer();
            return true;
        } else {
            return false;
        }
    }

    /**
     * Add new address to the customer @CustID
     * @param subTestID - will be placed to the  First Name field
     * @param boolean isBilling - if set, new address will be dafault billing address
     * @param boolean isShipping - if set, new address will be dafault shipping address
     *
     */
    function addAddress($params, $param, $isBilling, $isShipping)
    {
        $aData = $param ? $param : $this->aData;
        $this->setUiNamespace('admin/pages/customers/manage_customers/edit_customer_page');
        if (!$this->isElementPresent($this->getUiElement("elements/edit_page_open"))) {
            $this->doOpenCustomer($aData["CustID"]);
        }
        // Open Address Tab
        $this->waitForElement($this->getUiElement("tabs/addresses_tab"), 2);
        $this->click($this->getUiElement("tabs/addresses_tab"));
        $this->click($this->getUiElement("buttons/add_new_address"));
        $this->doAddAddress($params, $param, $isBilling, $isShipping);

        //Save Customer
        $this->saveAndVerifyForErrors();
    }

    /**
     *
     */
    public function editStoreCredit($params)
    {
        if ($this->isElementPresent($this->getUiElement("edit_customer_page/tabs/store_credit") . "[not (contains(@class,'active'))]")) {
            $this->click($this->getUiElement("edit_customer_page/tabs/store_credit"));
        }
        $this->select($this->getUiElement("edit_customer_page/selectors/store_credit_website"), 'website');
        $this->type($this->getUiElement("edit_customer_page/inputs/store_credit_update_balance"), $aData['balance']);
        if ($aData['Notify_email']) {
            $this->click($this->getUiElement("edit_customer_page/inputs/store_credit_notify_email"));
            $this->select($this->getUiElement("edit_customer_page/selectors/store_credit_email_from"), $aData['storeview']);
        }
        $this->type($this->getUiElement("edit_customer_page/inputs/store_credit_comment"), $aData['comment']);
        $this->clickAndWait($this->getUiElement("edit_customer_page/buttons/save_and_continue"));
    }

    /**
     * 
     */
    public function editRewardPoints($params)
    {
        if ($this->isElementPresent($this->getUiElement("edit_customer_page/tabs/reward_points") . "[not (contains(@class,'active'))]")) {
            $this->click($this->getUiElement("edit_customer_page/tabs/reward_points"));
        }
        $this->select($this->getUiElement("edit_customer_page/selectors/reward_points_store"), $aData['reward_points_store']);
        //if ((-4294967295<=$aData['reward points'])||($aData['reward points']=>4294967295)) {
            $this->type($this->getUiElement("edit_customer_page/inputs/reward_points_update_points"), $aData['reward points']);
        //}
        $this->type($this->getUiElement("edit_customer_page/inputs/reward_points_comment"), $aData['reward_points_comment']);
        $this->clickAndWait($this->getUiElement("edit_customer_page/buttons/save_and_continue"));
    }

    /**
     *
     */
    public function doAddAddress($params, $param, $isBilling, $isShipping)
    {
        $aData = $param ? $param : $this->aData;

        if ($this->isElementPresent($this->getUiElement("edit_customer_page/tabs/addresses_tab") . "[not (contains(@class,'active'))]")) {
            $this->click($this->getUiElement("edit_customer_page/tabs/addresses_tab"));
        }
        $address_qty = count($this->getUiElement("elements/edit_address")) - 1;
        $this->printInfo($address_qty);
        if ($this->isElementPresent($this->getUiElement("elements/new_address_container", $address_qty))) {

        }
        // Fill New Address
        foreach ($params as $key => $val) {
            $this->fillTextField($key, $val, $address_qty);
        }
        $this->selectCountry($param);
        $this->selectState($param);
        // Fill New Address End
        $this->fillAddressLines($param, $address_qty);
        // Specify Default Billing Address
        if ($isBilling) {
            $this->printInfo($address_qty);
            $billing = array($address_qty+2,'Billing');
            $this->printInfo($billing);
            $this->click($this->getUiElement("elements/is_billing_shipping", $billing));
        }
        if ($isShipping) {
            $this->printInfo($address_qty);
            $shipping = array($address_qty+2,'Shipping');
            $this->printInfo($shipping);
            $this->click($this->getUiElement("elements/is_billing_shipping", $shipping));
        }
    }

    /**
     * Check values of added address.
     * @param CustID - ID customer
     * @param TestID - address with TestID in the First Name field will be used
     * @param boolean isBilling - if set, address will be checked as dafault billing address
     * @param boolean isShipping - if set, address will be checked as dafault shipping address
     *
     */
    function verifyAddress($params, $param, $isBilling, $isShipping, $parametr)
    {
        $aData = $param ? $param : $this->aData;
        // Verify Section Start
        // Open Address Tab
        $this->click($this->getUiElement("edit_customer_page/tabs/addresses_tab"));
        $this->click($this->getUiElement("edit_customer_page/elements/address_list_container", $aData['First_name']));
        $i = 0;
        foreach ($parametr as $key => $val) {
            $this->checkTextField($key, $val);
            $i++;
        }
        if ($this->checkAddressLines("Street Address Sample L1", "Street Address Sample L2")) {
            $i++;
        }
        if ($this->checkCountry("United States")) {
            $i++;
        }
        if ($this->checkState("California")) {
            $i++;
        }
        // Check Default Billing and Shipping Address Options
        $this->checkIsDefaultState($isBilling, $isShipping);
        if ($i == 0) {
            $this->printInfo("address is completely different");
            return false;
        } elseif (($i < 9) || ($i > 0)) {
            $this->printInfo("address is partly different");
            return false;
        } elseif ($i == 9) {
            $this->printInfo("address is the same");
            return true;
        } else {
            $this->printInfo("address is shit the brick!!");
            return false;
        }
        // Verify Section End
    }

    /**
     * Select $countryName in countries dropdown
     * @param $tableBaseURL - xpath for table with address fields
     * @param $countryName - country name
     * @return boolean
     */
    public function selectCountry($param/* tableBaseURL, $countryName */)
    {
        $aData = $param ? $param : $this->aData;
        if ($this->isElementPresent($this->getUiElement("elements/new_address_container"))) {
            $this->select($this->getUiElement("elements/new_address_container") . $this->getUiElement("selectors/country"), $aData['country']);
            return true;
        } else {
            return false;
        }
    }

    /**
     * Select $stateName in States dropdown
     *
     * @param $tableBaseURL - xpath for table with address fields
     * @param $stateName - state name
     * @return boolean
     */
    public function selectState($param)
    {
        $aData = $param ? $param : $this->aData;
        $this->waitForElement(($this->getUiElement("selectors/state")), 1);
        if ($this->isElementPresent($this->getUiElement("selectors/state"))) {
            if ($this->isElementPresent($this->getUiElement("elements/new_address_container"))) {
                $this->select($this->getUiElement("elements/new_address_container") . $this->getUiElement("selectors/state"), $aData['state']);
                return true;
            } else {
                $this->select($this->getUiElement("selectors/state"), $aData['state']);
            }
        } else {
            $this->printInfo("There are no states for this country");
            return true;
        }
    }

    /**
     * Fill Address Line1 and Address Line2 with $line1 and $line2 values
     *
     * @param $tableBaseURL - xpath for table with address fields
     * @param $line1 - address line1 value
     * @param $line2 - address line1 value
     */
    public function fillAddressLines($param, $address_qty)
    {
        $aData = $param ? $param : $this->aData;
        if ($this->isElementPresent($this->getUiElement("elements/new_address_container", $address_qty))) {
            $this->type($this->getUiElement("elements/new_address_container", $address_qty) . $this->getUiElement("inputs/street_line", 0), $aData['street_line_1']);
            $this->type($this->getUiElement("elements/new_address_container", $address_qty) . $this->getUiElement("inputs/street_line", 1), $aData['street_line_2']);
        } else {
            $this->type($this->getUiElement("inputs/street_line", 0), $aData['street_line_1']);
            $this->type($this->getUiElement("inputs/street_line", 1), $aData['street_line_2']);
        }
    }

    /**
     * Place $fieldValue to the text input corresponding to $fieldName description
     *
     * @param $tableBaseURL - xpath for table with address fields
     * @param $fieldValue - value to fill
     * @param $fieldName - name of corresponding field
     */
    public function fillTextField($fieldName, $fieldValue, $address_qty)
    {
        if ($this->isElementPresent($this->getUiElement("elements/new_address_container", $address_qty))) {
            if ($this->isElementPresent($this->getUiElement("elements/text_fields", $fieldName))) {
                $this->type($this->getUiElement("elements/text_fields", $fieldName), $fieldValue);
                $this->printInfo("element  " . $fieldName . " filled with value " . $fieldValue);
            }
        } else {
            $this->type($this->getUiElement("elements/text_fields", $fieldName), $fieldValue);
            $this->printInfo("element  " . $fieldName . " filled with value " . $fieldValue);
        }
    }

    /**
     * Check existence $fieldValue in the text input corresponding to $fieldName description
     *
     * @param $tableBaseURL - xpath for table with address fields
     * @param $fieldValue -  value to fill
     * @param $fieldName - name of corresponding field
     *
     * @return setVerificationErrors if not matched
     */
    public function checkTextField($fieldName, $fieldValue)
    {
        $array = array($fieldName, $fieldValue);
        //$array[]= $fieldName;
        //$array[]= $fieldValue;
        try {
            $this->assertTrue($this->isElementPresent($this->getUiElement("edit_customer_page/elements/check_text_fields", $array)));
            $this->printInfo("field " . $fieldName . " is checked for value " . $fieldValue);
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            $this->setVerificationErrors("checkTextField failed: " . $fieldName . " is not equal to " . $fieldValue);
        }
    }

    /**
     * Check existence Address $Line1 and $Line2 in the Address fields
     *
     * @param $tableBaseURL - xpath for table with address fields
     * @param $line1 - value to fill
     * @param $line2 - name of corresponding field
     *
     * @return setVerificationErrors if not matched
     */
    public function checkAddressLines($line1, $line2)
    {
        $array_0 = array(0, $line1);
        $this->printInfo("checkAddressLines started");
        try {
            $this->assertTrue($this->isElementPresent($this->getUiElement("edit_customer_page/elements/check_street_line", $array_0)));
            $this->printInfo("checkAddressLines tryes 1");
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            $this->setVerificationErrors("checkAddressLine1 failed: is not equal to " . $line1);
        }
        $array_1 = array(1, $line2);
        try {
            $this->assertTrue($this->isElementPresent($this->getUiElement("edit_customer_page/elements/check_street_line", $array_1)));
            $this->printInfo("checkAddressLines tryes 2");
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            $this->setVerificationErrors("checkAddressLine2 failed: is not equal to " . $line2);
        }
    }

    /**
     * Check existence country selector with $country selected items
     *
     * @param $tableBaseURL - xpath for table with address fields
     * @param $country -  Country name
     *
     * @return setVerificationErrors if not matched
     */
    public function checkCountry($country)
    {
        try {
            $this->assertTrue($this->isElementPresent($this->getUiElement("edit_customer_page/elements/check_country", $country)));
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            $this->setVerificationErrors("checkCountry failed: is not equal to " . $country);
        }
    }

    /**
     * Check existence state selector with $state selected items
     *
     * @param $tableBaseURL - xpath for table with address fields
     * @param $state -  State name
     *
     * @return setVerificationErrors if not matched
     */
    public function checkState($state)
    {
        try {
            $this->assertTrue($this->isElementPresent($this->getUiElement("edit_customer_page/elements/check_state", $state)));
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            $this->setVerificationErrors("checkState failed:  is not equal to " . $state);
        }
    }

    /**
     * Check $isBilling, $isShipping attributes in opened customer address
     *
     * @param $tableBaseURL - xpath for table with address fields
     * @param $state -  State name
     *
     * @return setVerificationErrors if not matched
     */
    public function checkIsDefaultState($isBilling, $isShipping)
    {
        if ($isBilling) {
            // isBilling...
            try {
                $this->printInfo($this->isElementPresent($this->getUiElement("edit_customer_page/elements/billing_shipping_checked", 'Billing')));
                $this->assertTrue($this->isElementPresent($this->getUiElement("edit_customer_page/elements/billing_shipping_checked", 'Billing')));
                $this->printInfo("checked addres is Billing");
            } catch (PHPUnit_Framework_AssertionFailedError $e) {
                $this->setVerificationErrors("checkIsDefaultShippingAddress failed");
            }
        } else {
            // is Not Billing Address...
            try {
                $this->assertTrue(!$this->isElementPresent($this->getUiElement("edit_customer_page/elements/billing_shipping_checked", 'Billing')));
                $this->printInfo("checked addres is not Billing");
            } catch (PHPUnit_Framework_AssertionFailedError $e) {
                $this->setVerificationErrors("checkIsNotDefaultShippingAddress failed");
            }
        }
        if ($isShipping) {
            // isShipping...
            try {
                $this->assertTrue($this->isElementPresent($this->getUiElement("edit_customer_page/elements/billing_shipping_checked", 'Shipping')));
                $this->printInfo("checked addres is Shipping");
            } catch (PHPUnit_Framework_AssertionFailedError $e) {
                $this->setVerificationErrors("checkIsDefaultShippingAddress failed");
            }
        } else {
            // is Not Shipping Address...
            try {
                $this->assertTrue(!$this->isElementPresent($this->getUiElement("edit_customer_page/elements/billing_shipping_checked", 'Shipping')));
                $this->printInfo("checked addres is not Shipping");
            } catch (PHPUnit_Framework_AssertionFailedError $e) {
                $this->setVerificationErrors("checkIsNotDefaultShippingAddress failed");
            }
        }
    }

}
