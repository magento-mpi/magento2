<?php
/**
 * Admin customer framework model
 *
 * @author Magento Inc.
 */
class Model_Admin_Customer_Address extends Model_Admin_Customer
{

    /**
     * Click button "Save Customer". Asserting "The Customer has been saved" text
     *
     */
    public function doAdminSaveCustomer()
    {
        $saveCustomer = $this->getUiElement("admin/customer/button/savecustomer");
        $this->waitForElement($saveCustomer, 20);
        $this->click($saveCustomer);
        $this->waitForPageToLoad("90000");
        return $this->assertTrue(
            $this->isTextPresent(
                $this->getUiElement("admin/customer/message/customersaved")
            )
        );
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
            $CustID = $this->customerId;
        }

        $this->click($this->getUiElement("admin/topmenu/customer/managecustomers"));
        $this->waitForPageToLoad("90000");
        $this->type("filter_entity_id_from", $CustID);
        $this->type("filter_entity_id_to", $CustID);
        $this->click($this->getUiElement("admin/customer/button/search"));

        $this->pleaseWait();

        if ($this->waitForElement("//table[@id='customerGrid_table']//td[contains(text(),\"$CustID\")]",30)) {
            $this->click("//table[@id='customerGrid_table']//td[contains(text(),\"$CustID\")]");
            $this->waitForPageToLoad("90000");
            return true;
        } else {
            $this->setVerificationErrors("Customer ".$CustID." could not be loaded");
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
            $CustID = $this->customerId;
        }
        $this->click($this->getUiElement("admin/topmenu/customer/managecustomers"));
        $this->waitForPageToLoad("90000");
        $this->type("filter_entity_id_from", $CustID);
        $this->type("filter_entity_id_to", $CustID);
        $this->click($this->getUiElement("admin/customer/button/search"));

        $this->pleaseWait();
        sleep(1);

        return $this->isElementPresent("//table[@id='customerGrid_table']//td[contains(text(),\"$CustID\")]");
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
            $CustID = $this->customerId;
        }
        if ($this->doOpenCustomer($this->$CustID)) {
        // Open Address Tab
        $this->click("//a[@id='customer_info_tabs_addresses']/span");
        return $this->isElementPresent($this->getUiElement("admin/customer/address/managepanel")."//li[contains(address, '".$FirstName." Lname')]");
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
            $CustID = $this->customerId;
        }
        if ($this->doOpenCustomer($CustID)) {
            // Remove All Addresses
            while ($this->isElementPresent($this->getUiElement("admin/customer/address/managepanel")."//ul[contains(@id,'address_list')]//li//img[@alt='Remove address']")) {
                $this->click($this->getUiElement("admin/customer/address/managepanel")."//ul[contains(@id,'address_list')]//li//img[@alt='Remove address']");
                $this->getConfirmation();
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
            $CustID = $this->customerId;
        }
        if ($this->doOpenCustomer($CustID)) {
            // Remove Test Addresses
            while ($this->isElementPresent($this->getUiElement("admin/customer/address/managepanel")."//ul[contains(@id,'address_list')]//li[contains(address,'".$FirstName."')]//img[@alt='Remove address']")) {
                $this->click($this->getUiElement("admin/customer/address/managepanel")."//ul[contains(@id,'address_list')]//li[contains(address,'".$FirstName."')]//img[@alt='Remove address']");
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
    function addAddress($params, $isBilling, $isShipping)
    {
        $this->doOpenCustomer($this->_customerId);
        // Open Address Tab
        $this->click("//a[@id='customer_info_tabs_addresses']/span");
        $this->click("add_address_button");

        // Fill New Address
        $elementXpath = $this->getUiElement("admin/customer/address/newaddrfieldstable");

        foreach($params as $key => $val) {
            $this->fillTextField($elementXpath, $key, $val);
        }

        $this->selectCountry($elementXpath, "United States");
        $this->selectState($elementXpath, "California");

        $this->fillAddressLines($elementXpath,"Street Address Sample L1", "Street Address Sample L2");
        // Fill New Address End

        // Specify Default Billing Address
        if ($isBilling) {
            $this->click($this->getUiElement("AddrManagePanel")."//label[con tains(text(),'Billing')]");
        }
        if ($isShipping) {
            $this->click($this->getUiElement("AddrManagePanel")."//label[contains(text(),'Shipping')]");
        }

        //Save Customer
        $this->doAdminSaveCustomer();
    }

    /**
     * Check values of added address.
     * @param CustID - ID customer
     * @param TestID - address with TestID in the First Name field will be used
     * @param boolean isBilling - if set, address will be checked as dafault billing address
     * @param boolean isShipping - if set, address will be checked as dafault shipping address
     *
     */
    function verifyAddress($params, $isBilling, $isShipping)
    {
        // Verify Section Start
        $this-> doOpenCustomer( $this->$customerID);
        // Open Address Tab
        $this->click("//a[@id='customer_info_tabs_addresses']/span");

        $this->click($this->getUiElement("AddrManagePanel")."//li[contains(address, '". $subTestID." Lname')]//img[@alt='Edit address']");

        $elementXpath = $this->getUiElement("admin/customer/address/editdfields");

        foreach($params as $key => $val) {
            $this->checkTextField($elementXpath, $key, $val);
        }

        $this->checkAddressLines($elementXpath,"Street Address Sample L1", "Street Address Sample L2");

        $this->checkCountry($elementXpath, "United Kingdom");
        $this->checkState($elementXpath, "California");
        // Verify Section End

        // Check Default Billing and Shipping Address Options
        $this->checkIsDefaultState($this->getUiElement('AddrManagePanel'), $isBilling, $isShipping);
    }

    /**
     * Select $countryName in countries dropdown
     * @param $tableBaseURL - xpath for table with address fields
     * @param $countryName - country name
     * @return boolean
     */
    public function selectCountry($tableBaseURL, $countryName)
    {
        if (!$this->isElementPresent($tableBaseURL."//tr[contains(td,'Country')]/td[contains(@class,'value')]//option[@selected='selected' and text()='".$countryName."']")) {
            $this->select($tableBaseURL."//tr[contains(td,'Country')]/td[contains(@class,'value')]/select", $countryName);
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
        $this->select($tableBaseURL . "//tr[contains(td,'State/Province')]/td[contains(@class,'value')]/select", $stateName);
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
        $this->type($tableBaseURL."//tr[contains(td,'Street Address')]/td[contains(@class,'value')]//div[1]/input", $line1);
        $this->type($tableBaseURL."//tr[contains(td,'Street Address')]/td[contains(@class,'value')]//div[2]/input", $line2);
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
        $this->type($tableBaseURL."//tr[contains(td,'".$fieldName."')]/td[contains(@class,'value')]/input", $fieldValue);
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
            $this->assertTrue($this->isElementPresent($tableBaseURL .  "//tr[contains(td,'".$fieldName."')]/td[contains(@class,'value')]/input[@value='".$fieldValue."']"));
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            $this->setVerificationErrors("checkTextField failed: ".$fieldName." is not equal to ".$fieldValue);
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
            $this->assertTrue($this->isElementPresent($tableBaseURL .  "//tr[contains(td,'Street Address')]/td[contains(@class,'value')]//div[1]/input[@value='".$line1."']"));
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            $this->setVerificationErrors("checkAddressLine1 failed: is not equal to ".$line1);
        }

        try {
            $this->assertTrue($this->isElementPresent($tableBaseURL .  "//tr[contains(td,'Street Address')]/td[contains(@class,'value')]//div[2]/input[@value='".$line2."']"));
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            $this->setVerificationErrors("checkAddressLine2 failed: is not equal to ".$line2);
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
            $this->assertTrue($this->isElementPresent($tableBaseURL."//tr[contains(td,'Country')]/td[contains(@class,'value')]//option[@selected='selected' and text()='".$country."']"));
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            $this->setVerificationErrors("checkCountry failed: is not equal to ".$country);
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
            $this->assertTrue($this->isElementPresent( $tableBaseURL. "//tr[contains(td,'State/Province')]/td[contains(@class,'value')]//option[@selected='selected' and text()='".$state."']"));
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            $this->setVerificationErrors("checkState failed:  is not equal to ".$state);
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
                $this->assertTrue($this->isElementPresent($AddrManagePanel."//input[@checked='checked' and contains(@title,'Billing')]"));
            } catch (PHPUnit_Framework_AssertionFailedError $e) {
                $this->setVerificationErrors("checkIsDefaultShippingAddress failed");
            }
        } else {
            // is Not Billing Address...
            try {
                $this->assertTrue(!$this->isElementPresent($AddrManagePanel."//input[@checked='checked' and contains(@title,'Billing')]"));
            } catch (PHPUnit_Framework_AssertionFailedError $e) {
                $this->setVerificationErrors("checkIsNotDefaultShippingAddress failed");
            }
        }
        if ($isShipping) {
            // isShipping...
            try {
                $this->assertTrue($this->isElementPresent($AddrManagePanel."//input[@checked='checked' and contains(@title,'Shipping')]"));
            } catch (PHPUnit_Framework_AssertionFailedError $e) {
                $this->setVerificationErrors("checkIsDefaultShippingAddress failed");
            }
        } else {
            // is Not Shipping Address...
            try {
                $this->assertTrue(!$this->isElementPresent($AddrManagePanel."//input[@checked='checked' and contains(@title,'Shipping')]"));
            } catch (PHPUnit_Framework_AssertionFailedError $e) {
                $this->setVerificationErrors("checkIsNotDefaultShippingAddress failed");
            }
        }
    }

}

