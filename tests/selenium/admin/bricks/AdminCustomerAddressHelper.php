<?php

class AdminCustomerAddressHelper {
    protected $_object;
    public  $adminhelper;

    public function  __construct($object = null) {
        $this->_object = $object;
        $this->adminhelper =  new AdminHelper($this->_object);
    }

    public  function getAdminHelper() {
        return  $this->adminhelper;
    }

    public function doAdminSaveCustomer() {
        for ($second = 0; ; $second++) {
            if ($second >= 60) $this->_object->fail("pleasewait timeout");
            try {
                if ($this->_object->isElementPresent("//div[contains(@id,'page:main-container')]//div[contains(@class,'content-header')]//p[contains(@class,'form-buttons')]//button[contains(span,'Save Customer')]")) break;
            } catch (Exception $e) {

            }
            sleep(1);
        }

        $this->_object->click("//div[contains(@id,'page:main-container')]//div[contains(@class,'content-header')]//p[contains(@class,'form-buttons')]//button[contains(span,'Save Customer')]");
        $this->_object->waitForPageToLoad("90000");
        $this->_object->assertTrue($this->_object->isTextPresent("The customer has been saved."));
        return true;
    }

    public function doOpenCustomer($CustID) {

        $this->_object->click("//div[@class=\"nav-bar\"]//a[span=\"Manage Customers\"]");
        $this->_object->waitForPageToLoad("90000");
        $this->_object->type("filter_entity_id_from", $CustID);
        $this->_object->type("filter_entity_id_to", $CustID);
        $this->_object->click("//div[@id='customerGrid']//button[span='Search']");

        $this->adminhelper->pleaseWait();
        $loaded=false;

        for ($second = 0; ; $second++) {
            if ($second >= 30) break;//$this->_object->fail("timeout for ".$CustID." customer loading");
            try {
                if ($this->_object->isElementPresent("//table[@id='customerGrid_table']//td[contains(text(),\"$CustID\")]")) {
                    $loaded=true;
                    break;
                }
            } catch (Exception $e) {

            }
            sleep(1);
        }

        if ($loaded) {
            $this->_object->click("//table[@id='customerGrid_table']//td[contains(text(),\"$CustID\")]");
            $this->_object->waitForPageToLoad("90000");
        } else {
            $this->_object->setVerificationErrors("Customer ".$CustID." could not be loaded");
        }
        return $loaded;
    }

    public function isCustomerPresent($CustID) {
         echo ("isCustomerPresent started...");
        $this->_object->click("//div[@class=\"nav-bar\"]//a[span=\"Manage Customers\"]");
        $this->_object->waitForPageToLoad("90000");
        $this->_object->type("filter_entity_id_from", $CustID);
        $this->_object->type("filter_entity_id_to", $CustID);
        $this->_object->click("//div[@id='customerGrid']//button[span='Search']");

        $this->pleaseWait();
        sleep(1);

        return $this->_object->isElementPresent("//table[@id='customerGrid_table']//td[contains(text(),\"$CustID\")]");
    }

    public function isAddressPresent($CustID,$TestID) {
        return $this->_object->isElementPresent("//table[contains(@class,'form-edit')]//td[contains(@class,'address-list')]//ul[contains(@id,'address_list')]//li[contains(address,'".$TestID."')]");
    }

    public function isCustomerAddressPresent($AddrManagePanel,$CustID,$TestID) {
        $this-> doOpenCustomer($CustID);
        // Open Address Tab
        $this->_object->click("//a[@id='customer_info_tabs_addresses']/span");
        //echo($AddrManagePanel."//li[contains(address, '".$TestID." Lname')]");
        return $this->_object->isElementPresent($AddrManagePanel."//li[contains(address, '".$TestID." Lname')]");
    }

    public function delAllAddresses($AddrManagePanel, $CustID) {

        if ($this->doOpenCustomer($CustID)) {
        // Remove All Addresses
        while ($this->_object->isElementPresent("//table[contains(@class,'form-edit')]//td[contains(@class,'address-list')]//ul[contains(@id,'address_list')]//li//img[@alt='Remove address']")) {
            $this->_object->click("//table[contains(@class,'form-edit')]//td[contains(@class,'address-list')]//ul[contains(@id,'address_list')]//li//img[@alt='Remove address']");
            $this->_object->getConfirmation();
        };
        $this->doAdminSaveCustomer();
        }
    }

    public function delAddresses($AddrManagePanel, $CustID, $TestID) {

        if ($this->doOpenCustomer($CustID)) {
            // Remove Test Addresses
            while ($this->_object->isElementPresent("//table[contains(@class,'form-edit')]//td[contains(@class,'address-list')]//ul[contains(@id,'address_list')]//li[contains(address,'".$TestID."')]//img[@alt='Remove address']")) {
                $this->_object->click("//table[contains(@class,'form-edit')]//td[contains(@class,'address-list')]//ul[contains(@id,'address_list')]//li[contains(address,'".$TestID."')]//img[@alt='Remove address']");
                $this->_object->getConfirmation();
            };
            $this->doAdminSaveCustomer();
            return true;
        } else {
            return false;
        }
    }

    public function selectCountry($tableBaseURL, $countryName) {
        if (!$this->_object->isElementPresent($tableBaseURL."//tr[contains(td,'Country')]/td[contains(@class,'value')]//option[@selected='selected' and text()='".$countryName."']")) {
            $this->_object->select($tableBaseURL."//tr[contains(td,'Country')]/td[contains(@class,'value')]/select", $countryName);
            for ($second = 0; ; $second++) {
                if ($second >= 60) $this->_object->fail("timeout");
                try {
                    if (!$this->_object->isElementPresent("//div[@id='loading-mask' and contains(@style,'display: none')]")) break;
                } catch (Exception $e) {

                }
                sleep(1);
            }

            for ($second = 0; ; $second++) {
                if ($second >= 60) $this->_object->fail("timeout");
                try {
                    if ($this->_object->isElementPresent("//div[@id='loading-mask' and contains(@style,'display: none')]")) break;
                } catch (Exception $e) {

                }
                sleep(1);
            }
        }
    }

    public function selectState($tableBaseURL, $state) {
        $this->_object->select($tableBaseURL . "//tr[contains(td,'State/Province')]/td[contains(@class,'value')]/select", $state);
    }

    public function fillAddressLines($tableBaseURL, $Line1,$Line2) {
        $this->_object->type($tableBaseURL."//tr[contains(td,'Street Address')]/td[contains(@class,'value')]//div[1]/input", $Line1);
        $this->_object->type($tableBaseURL."//tr[contains(td,'Street Address')]/td[contains(@class,'value')]//div[2]/input", $Line2);
    }
    
    public function fillTextField($tableBaseURL,$fieldName, $fieldValue) {
        $this->_object->type($tableBaseURL."//tr[contains(td,'".$fieldName."')]/td[contains(@class,'value')]/input", $fieldValue);
    }

    public function checkTextField($tableBaseURL,$fieldName, $fieldValue) {
        try {
            $this->_object->assertTrue($this->_object->isElementPresent($tableBaseURL .  "//tr[contains(td,'".$fieldName."')]/td[contains(@class,'value')]/input[@value='".$fieldValue."']"));
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            $this->_object->setVerificationErrors("checkTextField failed: ".$fieldName." is not equal to ".$fieldValue);
        }
    }

    public function checkAddressLines($tableBaseURL, $Line1,$Line2) {
        try {
            $this->_object->assertTrue($this->_object->isElementPresent($tableBaseURL .  "//tr[contains(td,'Street Address')]/td[contains(@class,'value')]//div[1]/input[@value='".$Line1."']"));
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            $this->_object->setVerificationErrors("checkAddressLine1 failed: is not equal to ".$Line1);
        }

        try {
            $this->_object->assertTrue($this->_object->isElementPresent($tableBaseURL .  "//tr[contains(td,'Street Address')]/td[contains(@class,'value')]//div[2]/input[@value='".$Line2."']"));
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            $this->_object->setVerificationErrors("checkAddressLine2 failed: is not equal to ".$Line2);
        }
    }

    public function checkCountry ($tableBaseURL, $country) {
        try {
            $this->_object->assertTrue($this->_object->isElementPresent($tableBaseURL."//tr[contains(td,'Country')]/td[contains(@class,'value')]//option[@selected='selected' and text()='".$country."']"));
            //$this->assertTrue($this->isElementPresent($AddrFieldsTable .  "//tr[contains(td,'State/Province')]/td[contains(@class,'value')]/input[@value='London']"));
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            $this->_object->setVerificationErrors("checkCountry failed: is not equal to ".$country);
        }

    }

    public function checkState ($tableBaseURL, $state) {
        try {
            $this->_object->assertTrue($this->_object->isElementPresent( $tableBaseURL. "//tr[contains(td,'State/Province')]/td[contains(@class,'value')]//option[@selected='selected' and text()='".$state."']"));
        } catch (PHPUnit_Framework_AssertionFailedError $e) {
            $this->_object->setVerificationErrors("checkState failed:  is not equal to ".$state);
        }
    }

    public function checkIsDefaultState ($AddrManagePanel, $isBilling, $isShipping) {
        if ($isBilling) {
            // isBilling...
            try {
                $this->_object->assertTrue($this->_object->isElementPresent($AddrManagePanel."//input[@checked='checked' and contains(@title,'Billing')]"));
            } catch (PHPUnit_Framework_AssertionFailedError $e) {
                $this->_object->setVerificationErrors("checkIsDefaultShippingAddress failed");
            }
        } else {
            // is Not Billing Address...
            try {
                $this->_object->assertTrue(!$this->_object->isElementPresent($AddrManagePanel."//input[@checked='checked' and contains(@title,'Billing')]"));
            } catch (PHPUnit_Framework_AssertionFailedError $e) {
                $this->_object->setVerificationErrors("checkIsNotDefaultShippingAddress failed");
            }
        }
        if ($isShipping) {
            // isShipping...
            try {
                $this->_object->assertTrue($this->_object->isElementPresent($AddrManagePanel."//input[@checked='checked' and contains(@title,'Shipping')]"));
            } catch (PHPUnit_Framework_AssertionFailedError $e) {
                $this->_object->setVerificationErrors("checkIsDefaultShippingAddress failed");
            }
        } else {
            // is Not Shipping Address...
            try {
                $this->_object->assertTrue(!$this->_object->isElementPresent($AddrManagePanel."//input[@checked='checked' and contains(@title,'Shipping')]"));
            } catch (PHPUnit_Framework_AssertionFailedError $e) {
                $this->_object->setVerificationErrors("checkIsNotDefaultShippingAddress failed");
            }
        }
    }

}