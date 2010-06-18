<?php

class Helper_Admin_Customer_Address extends Helper_Admin {
    protected $_uiMap = array(
        "NewAddrFieldsTable"    => "//div[@id='address_form_container']//div[contains(@id,'form_new_item')     and not(contains(@style,'display: none'))]//table[contains(@class,'form-list')]/tbody",
        "EditAddrFieldsTable"   => "//div[@id='address_form_container']//div[contains(@id,'form_address_item') and not(contains(@style,'display: none'))]//table[contains(@class,'form-list')]/tbody",
        "AddrManagePanelActive" => "//table[contains(@class,'form-edit')]//td[contains(@class,'address-list')]//ul[contains(@id,'address_list')]//li[contains(@class,'on')]",
        "AddrManagePanel"       => "//table[contains(@class,'form-edit')]//td[contains(@class,'address-list')]//ul[contains(@id,'address_list')]//li[contains(@class,'on')]"
    );

    public function doAdminSaveCustomer() {
        for ($second = 0; ; $second++) {
            if ($second >= 60) $this->_context->fail("pleasewait timeout");
            try {
                if ($this->_context->isElementPresent("//div[contains(@id,'page:main-container')]//div[contains(@class,'content-header')]//p[contains(@class,'form-buttons')]//button[contains(span,'Save Customer')]")) break;
            } catch (Exception $e) {

            }
            sleep(1);
        }

        $this->_context->click("//div[contains(@id,'page:main-container')]//div[contains(@class,'content-header')]//p[contains(@class,'form-buttons')]//button[contains(span,'Save Customer')]");
        $this->_context->waitForPageToLoad("90000");
        $this->_context->assertTrue($this->_context->isTextPresent("The customer has been saved."));
        return true;
    }

    public function doOpenCustomer($CustID) {
        $this->_context->click("//div[@class=\"nav-bar\"]//a[span=\"Manage Customers\"]");
        $this->_context->waitForPageToLoad("90000");
        $this->_context->type("filter_entity_id_from", $CustID);
        $this->_context->type("filter_entity_id_to", $CustID);
        $this->_context->click("//div[@id='customerGrid']//button[span='Search']");

        $this->pleaseWait();
        $loaded=false;

        for ($second = 0; ; $second++) {
            if ($second >= 30) break;//$this->_context->fail("timeout for ".$CustID." customer loading");
            try {
                if ($this->_context->isElementPresent("//table[@id='customerGrid_table']//td[contains(text(),\"$CustID\")]")) {
                    $loaded=true;
                    break;
                }
            } catch (Exception $e) {

            }
            sleep(1);
        }

        if ($loaded) {
            $this->_context->click("//table[@id='customerGrid_table']//td[contains(text(),\"$CustID\")]");
            $this->_context->waitForPageToLoad("90000");
        } else {
            $this->_context->setVerificationErrors("Customer ".$CustID." could not be loaded");
        }
        return $loaded;
    }

    public function isCustomerPresent($CustID) {
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

    public function isAddressPresent($CustID,$TestID) {
        return $this->_context->isElementPresent("//table[contains(@class,'form-edit')]//td[contains(@class,'address-list')]//ul[contains(@id,'address_list')]//li[contains(address,'".$TestID."')]");
    }

    public function isCustomerAddressPresent($AddrManagePanel,$CustID,$TestID) {
        $this->doOpenCustomer($CustID);
        // Open Address Tab
        $this->_context->click("//a[@id='customer_info_tabs_addresses']/span");
        //echo($AddrManagePanel."//li[contains(address, '".$TestID." Lname')]");
        return $this->_context->isElementPresent($AddrManagePanel."//li[contains(address, '".$TestID." Lname')]");
    }

    public function delAllAddresses($AddrManagePanel, $CustID) {

        if ($this->doOpenCustomer($CustID)) {
        // Remove All Addresses
        while ($this->_context->isElementPresent("//table[contains(@class,'form-edit')]//td[contains(@class,'address-list')]//ul[contains(@id,'address_list')]//li//img[@alt='Remove address']")) {
            $this->_context->click("//table[contains(@class,'form-edit')]//td[contains(@class,'address-list')]//ul[contains(@id,'address_list')]//li//img[@alt='Remove address']");
            $this->_context->getConfirmation();
        };
        $this->doAdminSaveCustomer();
        }
    }

    public function delAddresses($AddrManagePanel, $CustID, $TestID) {

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

    public function selectCountry($tableBaseURL, $countryName) {
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

    public function selectState($tableBaseURL, $state) {
        $this->_context->select($tableBaseURL . "//tr[contains(td,'State/Province')]/td[contains(@class,'value')]/select", $state);
    }

    public function fillAddressLines($tableBaseURL, $Line1,$Line2) {
        $this->_context->type($tableBaseURL."//tr[contains(td,'Street Address')]/td[contains(@class,'value')]//div[1]/input", $Line1);
        $this->_context->type($tableBaseURL."//tr[contains(td,'Street Address')]/td[contains(@class,'value')]//div[2]/input", $Line2);
    }
    
    public function fillTextField($tableBaseURL,$fieldName, $fieldValue) {
        $this->_context->type($tableBaseURL."//tr[contains(td,'".$fieldName."')]/td[contains(@class,'value')]/input", $fieldValue);
    }

    public function checkTextField($tableBaseURL,$fieldName, $fieldValue) {
        try {
            $this->_context->assertTrue($this->_context->isElementPresent($tableBaseURL .  "//tr[contains(td,'".$fieldName."')]/td[contains(@class,'value')]/input[@value='".$fieldValue."']"));
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            $this->_context->setVerificationErrors("checkTextField failed: ".$fieldName." is not equal to ".$fieldValue);
        }
    }

    public function checkAddressLines($tableBaseURL, $Line1,$Line2) {
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

    public function checkCountry ($tableBaseURL, $country) {
        try {
            $this->_context->assertTrue($this->_context->isElementPresent($tableBaseURL."//tr[contains(td,'Country')]/td[contains(@class,'value')]//option[@selected='selected' and text()='".$country."']"));
            //$this->assertTrue($this->isElementPresent($AddrFieldsTable .  "//tr[contains(td,'State/Province')]/td[contains(@class,'value')]/input[@value='London']"));
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            $this->_context->setVerificationErrors("checkCountry failed: is not equal to ".$country);
        }

    }

    public function checkState ($tableBaseURL, $state) {
        try {
            $this->_context->assertTrue($this->_context->isElementPresent( $tableBaseURL. "//tr[contains(td,'State/Province')]/td[contains(@class,'value')]//option[@selected='selected' and text()='".$state."']"));
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            $this->_context->setVerificationErrors("checkState failed:  is not equal to ".$state);
        }
    }

    public function checkIsDefaultState ($AddrManagePanel, $isBilling, $isShipping) {
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
