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
     * Click button "Save Customer". Asserting "The Customer has been saved" text
     *
     */
    public function doOpenCustomer($CustID)
    {
        $this->_context->click($this->getUiElement("admin/topmenu/customer/managecustomers"));
        $this->_context->waitForPageToLoad("90000");
        $this->_context->type("filter_entity_id_from", $CustID);
        $this->_context->type("filter_entity_id_to", $CustID);
        $this->_context->click($this->getUiElement("admin/customer/button/search"));

        $this->pleaseWait();

        if (waitForElementNsec("//table[@id='customerGrid_table']//td[contains(text(),\"$CustID\")]",30)) {
            $this->_context->click("//table[@id='customerGrid_table']//td[contains(text(),\"$CustID\")]");
            $this->_context->waitForPageToLoad("90000");
        } else {
            $this->_context->setVerificationErrors("Customer ".$CustID." could not be loaded");
        }
        return $loaded;
    }

    public function isCustomerPresent($CustID)
    {
        echo ("isCustomerPresent started...");
        $this->_context->click("//div[@class=\"nav-bar\"]//a[span=\"Manage Customers\"]");
        $this->_context->waitForPageToLoad("90000");
        $this->_context->type("filter_entity_id_from", $CustID);
        $this->_context->type("filter_entity_id_to", $CustID);
        $this->_context->click("//div[@id='customerGrid']//button[span='Search']");

        $this->pleaseWait();
        sleep(1);

        return $this->_context->isElementPresent("//table[@id='customerGrid_table']//td[contains(text(),\"$CustID\")]");
    }

    public function isAddressPresent($CustID,$TestID)
    {
        return $this->_context->isElementPresent("//table[contains(@class,'form-edit')]//td[contains(@class,'address-list')]//ul[contains(@id,'address_list')]//li[contains(address,'".$TestID."')]");
    }

    public function isCustomerAddressPresent($AddrManagePanel,$CustID,$TestID) {
        $this->doOpenCustomer($CustID);
        // Open Address Tab
        $this->_context->click("//a[@id='customer_info_tabs_addresses']/span");
        //echo($AddrManagePanel."//li[contains(address, '".$TestID." Lname')]");
        return $this->_context->isElementPresent($AddrManagePanel."//li[contains(address, '".$TestID." Lname')]");
    }

    public function delAllAddresses($AddrManagePanel, $CustID)
    {
        if ($this->doOpenCustomer($CustID)) {
            // Remove All Addresses
            while ($this->_context->isElementPresent("//table[contains(@class,'form-edit')]//td[contains(@class,'address-list')]//ul[contains(@id,'address_list')]//li//img[@alt='Remove address']")) {
                $this->_context->click("//table[contains(@class,'form-edit')]//td[contains(@class,'address-list')]//ul[contains(@id,'address_list')]//li//img[@alt='Remove address']");
                $this->_context->getConfirmation();
            };
            $this->doAdminSaveCustomer();
        }
    }

    public function delAddresses($AddrManagePanel, $CustID, $TestID)
    {
        if ($this->doOpenCustomer($CustID)) {
            // Remove Test Addresses
            while ($this->_context->isElementPresent("//table[contains(@class,'form-edit')]//td[contains(@class,'address-list')]//ul[contains(@id,'address_list')]//li[contains(address,'".$TestID."')]//img[@alt='Remove address']")) {
                $this->_context->click("//table[contains(@class,'form-edit')]//td[contains(@class,'address-list')]//ul[contains(@id,'address_list')]//li[contains(address,'".$TestID."')]//img[@alt='Remove address']");
                $this->_context->getConfirmation();
            };
            $this->doAdminSaveCustomer();
            return true;
        } else {
            return false;
        }
    }

    public function selectCountry($tableBaseURL, $countryName)
    {
        if (!$this->_context->isElementPresent($tableBaseURL."//tr[contains(td,'Country')]/td[contains(@class,'value')]//option[@selected='selected' and text()='".$countryName."']")) {
            $this->_context->select($tableBaseURL."//tr[contains(td,'Country')]/td[contains(@class,'value')]/select", $countryName);
            for ($second = 0; ; $second++) {
                if ($second >= 60) $this->_context->fail("timeout");
                try {
                    if (!$this->_context->isElementPresent("//div[@id='loading-mask' and contains(@style,'display: none')]")) break;
                } catch (Exception $e) {

                }
                sleep(1);
            }

            for ($second = 0; ; $second++) {
                if ($second >= 60) $this->_context->fail("timeout");
                try {
                    if ($this->_context->isElementPresent("//div[@id='loading-mask' and contains(@style,'display: none')]")) break;
                } catch (Exception $e) {

                }
                sleep(1);
            }
        }
    }

    public function selectState($tableBaseURL, $state)
    {
        $this->_context->select($tableBaseURL . "//tr[contains(td,'State/Province')]/td[contains(@class,'value')]/select", $state);
    }

    public function fillAddressLines($tableBaseURL, $Line1,$Line2)
    {
        $this->_context->type($tableBaseURL."//tr[contains(td,'Street Address')]/td[contains(@class,'value')]//div[1]/input", $Line1);
        $this->_context->type($tableBaseURL."//tr[contains(td,'Street Address')]/td[contains(@class,'value')]//div[2]/input", $Line2);
    }

    public function fillTextField($tableBaseURL,$fieldName, $fieldValue)
    {
        $this->_context->type($tableBaseURL."//tr[contains(td,'".$fieldName."')]/td[contains(@class,'value')]/input", $fieldValue);
    }

    public function checkTextField($tableBaseURL,$fieldName, $fieldValue)
    {
        try {
            $this->_context->assertTrue($this->_context->isElementPresent($tableBaseURL .  "//tr[contains(td,'".$fieldName."')]/td[contains(@class,'value')]/input[@value='".$fieldValue."']"));
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            $this->_context->setVerificationErrors("checkTextField failed: ".$fieldName." is not equal to ".$fieldValue);
        }
    }

    public function checkAddressLines($tableBaseURL, $Line1,$Line2)
    {
        try {
            $this->_context->assertTrue($this->_context->isElementPresent($tableBaseURL .  "//tr[contains(td,'Street Address')]/td[contains(@class,'value')]//div[1]/input[@value='".$Line1."']"));
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            $this->_context->setVerificationErrors("checkAddressLine1 failed: is not equal to ".$Line1);
        }

        try {
            $this->_context->assertTrue($this->_context->isElementPresent($tableBaseURL .  "//tr[contains(td,'Street Address')]/td[contains(@class,'value')]//div[2]/input[@value='".$Line2."']"));
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            $this->_context->setVerificationErrors("checkAddressLine2 failed: is not equal to ".$Line2);
        }
    }

    public function checkCountry ($tableBaseURL, $country)
    {
        try {
            $this->_context->assertTrue($this->_context->isElementPresent($tableBaseURL."//tr[contains(td,'Country')]/td[contains(@class,'value')]//option[@selected='selected' and text()='".$country."']"));
            //$this->assertTrue($this->isElementPresent($AddrFieldsTable .  "//tr[contains(td,'State/Province')]/td[contains(@class,'value')]/input[@value='London']"));
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            $this->_context->setVerificationErrors("checkCountry failed: is not equal to ".$country);
        }

    }

    public function checkState ($tableBaseURL, $state)
    {
        try {
            $this->_context->assertTrue($this->_context->isElementPresent( $tableBaseURL. "//tr[contains(td,'State/Province')]/td[contains(@class,'value')]//option[@selected='selected' and text()='".$state."']"));
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            $this->_context->setVerificationErrors("checkState failed:  is not equal to ".$state);
        }
    }

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
