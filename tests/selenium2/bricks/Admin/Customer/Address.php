<?php

class Helper_Admin_Customer_Address extends Helper_Admin
{

    /**
     * Click button "Save Customer". Asserting "The Customer has been saved" text
     *
     */
    public function doAdminSaveCustomer()
    {
        $this->waitForElementNsec($this->getUiElement("admin/customer/button/savecustomer"),20);
        $this->_context->click($this->getUiElement("admin/customer/button/savecustomer"));
        $this->_context->waitForPageToLoad("90000");
        return $this->_context->assertTrue($this->_context->isTextPresent($this->getUiElement("admin/customer/message/customersaved")));
    }

    /**
     * Open customer for the editing
     * @param $CustID - contains reurn
     * @returns true on success
     *
     */
    public function doOpenCustomer($CustID = null)
    {
        if (null === $CustID) {
            $CustID = $this->_context->getCustomerId();
        }

        $this->_context->click($this->getUiElement("admin/topmenu/customer/managecustomers"));
        $this->_context->waitForPageToLoad("90000");
        $this->_context->type("filter_entity_id_from", $CustID);
        $this->_context->type("filter_entity_id_to", $CustID);
        $this->_context->click($this->getUiElement("admin/customer/button/search"));

        $this->pleaseWait();

        if ($this->waitForElementNsec("//table[@id='customerGrid_table']//td[contains(text(),\"$CustID\")]",30)) {
            $this->_context->click("//table[@id='customerGrid_table']//td[contains(text(),\"$CustID\")]");
            $this->_context->waitForPageToLoad("90000");
            return true;
        } else {
            $this->_context->setVerificationErrors("Customer ".$CustID." could not be loaded");
            return false;
        }
    }

    /**
     * Determine existing of customer with ID = $CustID
     * @param $CustID - customer ID
     * @returns true if exists
     *
     */
    public function isCustomerPresent($CustID = null)
    {
        if (null === $CustID) {
            $CustID = $this->_context->getCustomerId();
        }
        $this->_context->click($this->getUiElement("admin/topmenu/customer/managecustomers"));
        $this->_context->waitForPageToLoad("90000");
        $this->_context->type("filter_entity_id_from", $CustID);
        $this->_context->type("filter_entity_id_to", $CustID);
        $this->_context->click($this->getUiElement("admin/customer/button/search"));

        $this->pleaseWait();
        sleep(1);

        return $this->_context->isElementPresent("//table[@id='customerGrid_table']//td[contains(text(),\"$CustID\")]");
    }

    /**
     * Determine existing address with FirstName containing $FirstName in customer with ID = $CustID
     * @param $CustID - customer ID
     * @param $FirstName - part of "First name" field
     * @returns true such sddress exists
     *
     */
    public function isCustomerAddressPresent($FirstName, $CustID = null)
    {
        if (null === $CustID) {
            $CustID = $this->_context->getCustomerId();
        }
        if ($this->doOpenCustomer($this->$CustID)) {
        // Open Address Tab
        $this->_context->click("//a[@id='customer_info_tabs_addresses']/span");
        return $this->_context->isElementPresent($this->getUiElement("admin/customer/address/managepanel")."//li[contains(address, '".$FirstName." Lname')]");
        } else {
            return false;
        }

    }

    /**
     * Delete all addresses of customer with ID=$CustID
     * @param $CustID - customer ID
     *
     */
    public function delAllAddresses($CustID = null)
    {
        if (null === $CustID) {
            $CustID = $this->_context->getCustomerId();
        }
        if ($this->doOpenCustomer($CustID)) {
            // Remove All Addresses
            while ($this->_context->isElementPresent($this->getUiElement("admin/customer/address/managepanel")."//ul[contains(@id,'address_list')]//li//img[@alt='Remove address']")) {
                $this->_context->click($this->getUiElement("admin/customer/address/managepanel")."//ul[contains(@id,'address_list')]//li//img[@alt='Remove address']");
                $this->_context->getConfirmation();
            };
            $this->doAdminSaveCustomer();
        }
    }

    /**
     * Delete address with FirstName containing $FirstName in customer with ID = $CustID
     * @param $CustID - customer ID
     * @param $FirstName - part of "First name" field*
     * @return false if customer doesnot exisrs
     */
    public function delAddresses($FirstName, $CustID = null)
    {
        if (null === $CustID) {
            $CustID = $this->_context->getCustomerId();
        }
        if ($this->doOpenCustomer($CustID)) {
            // Remove Test Addresses
            while ($this->_context->isElementPresent($this->getUiElement("admin/customer/address/managepanel")."//ul[contains(@id,'address_list')]//li[contains(address,'".$FirstName."')]//img[@alt='Remove address']")) {
                $this->_context->click($this->getUiElement("admin/customer/address/managepanel")."//ul[contains(@id,'address_list')]//li[contains(address,'".$FirstName."')]//img[@alt='Remove address']");
                $this->_context->getConfirmation();
            };
            $this->doAdminSaveCustomer();
            return true;
        } else {
            return false;
        }
    }

    /**
     * Select $countryName in countries dropdown
     * @param $tableBaseURL - xpath for table with address fields
     * @param $countryName - country name
     * @return boolean
     */
    public function selectCountry($tableBaseURL, $countryName)
    {
        if (!$this->_context->isElementPresent($tableBaseURL."//tr[contains(td,'Country')]/td[contains(@class,'value')]//option[@selected='selected' and text()='".$countryName."']")) {
            $this->_context->select($tableBaseURL."//tr[contains(td,'Country')]/td[contains(@class,'value')]/select", $countryName);
            $this->pleaseWait();
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
    public function selectState($tableBaseURL, $stateName)
    {
        $this->_context->select($tableBaseURL . "//tr[contains(td,'State/Province')]/td[contains(@class,'value')]/select", $stateName);
    }

    /**
     * Fill Address Line1 and Address Line2 with $line1 and $line2 values
     *
     * @param $tableBaseURL - xpath for table with address fields
     * @param $line1 - address line1 value
     * @param $line2 - address line1 value
     */
    public function fillAddressLines($tableBaseURL, $line1, $line2)
    {
        $this->_context->type($tableBaseURL."//tr[contains(td,'Street Address')]/td[contains(@class,'value')]//div[1]/input", $line1);
        $this->_context->type($tableBaseURL."//tr[contains(td,'Street Address')]/td[contains(@class,'value')]//div[2]/input", $line2);
    }

    /**
     * Place $fieldValue to the text input corresponding to $fieldName description
     *
     * @param $tableBaseURL - xpath for table with address fields
     * @param $fieldValue - value to fill
     * @param $fieldName - name of corresponding field
     */
    public function fillTextField($tableBaseURL, $fieldName, $fieldValue)
    {
        $this->_context->type($tableBaseURL."//tr[contains(td,'".$fieldName."')]/td[contains(@class,'value')]/input", $fieldValue);
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
    public function checkTextField($tableBaseURL, $fieldName, $fieldValue)
    {
        try {
            $this->_context->assertTrue($this->_context->isElementPresent($tableBaseURL .  "//tr[contains(td,'".$fieldName."')]/td[contains(@class,'value')]/input[@value='".$fieldValue."']"));
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            $this->_context->setVerificationErrors("checkTextField failed: ".$fieldName." is not equal to ".$fieldValue);
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
    public function checkAddressLines($tableBaseURL, $line1, $line2)
    {
        try {
            $this->_context->assertTrue($this->_context->isElementPresent($tableBaseURL .  "//tr[contains(td,'Street Address')]/td[contains(@class,'value')]//div[1]/input[@value='".$line1."']"));
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            $this->_context->setVerificationErrors("checkAddressLine1 failed: is not equal to ".$line1);
        }

        try {
            $this->_context->assertTrue($this->_context->isElementPresent($tableBaseURL .  "//tr[contains(td,'Street Address')]/td[contains(@class,'value')]//div[2]/input[@value='".$line2."']"));
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            $this->_context->setVerificationErrors("checkAddressLine2 failed: is not equal to ".$line2);
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
    public function checkCountry ($tableBaseURL, $country)
    {
        try {
            $this->_context->assertTrue($this->_context->isElementPresent($tableBaseURL."//tr[contains(td,'Country')]/td[contains(@class,'value')]//option[@selected='selected' and text()='".$country."']"));
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            $this->_context->setVerificationErrors("checkCountry failed: is not equal to ".$country);
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
    public function checkState ($tableBaseURL, $state)
    {
        try {
            $this->_context->assertTrue($this->_context->isElementPresent( $tableBaseURL. "//tr[contains(td,'State/Province')]/td[contains(@class,'value')]//option[@selected='selected' and text()='".$state."']"));
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            $this->_context->setVerificationErrors("checkState failed:  is not equal to ".$state);
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
    public function checkIsDefaultState ($AddrManagePanel, $isBilling, $isShipping)
    {
        if ($isBilling) {
            // isBilling...
            try {
                $this->_context->assertTrue($this->_context->isElementPresent($AddrManagePanel."//input[@checked='checked' and contains(@title,'Billing')]"));
            } catch (PHPUnit_Framework_AssertionFailedError $e) {
                $this->_context->setVerificationErrors("checkIsDefaultShippingAddress failed");
            }
        } else {
            // is Not Billing Address...
            try {
                $this->_context->assertTrue(!$this->_context->isElementPresent($AddrManagePanel."//input[@checked='checked' and contains(@title,'Billing')]"));
            } catch (PHPUnit_Framework_AssertionFailedError $e) {
                $this->_context->setVerificationErrors("checkIsNotDefaultShippingAddress failed");
            }
        }
        if ($isShipping) {
            // isShipping...
            try {
                $this->_context->assertTrue($this->_context->isElementPresent($AddrManagePanel."//input[@checked='checked' and contains(@title,'Shipping')]"));
            } catch (PHPUnit_Framework_AssertionFailedError $e) {
                $this->_context->setVerificationErrors("checkIsDefaultShippingAddress failed");
            }
        } else {
            // is Not Shipping Address...
            try {
                $this->_context->assertTrue(!$this->_context->isElementPresent($AddrManagePanel."//input[@checked='checked' and contains(@title,'Shipping')]"));
            } catch (PHPUnit_Framework_AssertionFailedError $e) {
                $this->_context->setVerificationErrors("checkIsNotDefaultShippingAddress failed");
            }
        }
    }

}
