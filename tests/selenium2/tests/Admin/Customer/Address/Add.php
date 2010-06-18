<?php

class Admin_Customer_Address_Add extends Test_Admin_Customer_Abstract
{
    function addAddress($CustID, $TestID, $isBilling, $isShipping) {
        $this->_helper->doOpenCustomer($CustID);
        // Open Address Tab
        $this->click("//a[@id='customer_info_tabs_addresses']/span");
        $this->click("add_address_button");
        
        // Fill New Address
        $elementXpath = $this->_helper->getUiElement('NewAddrFieldsTable');
        
        $this->_helper->fillTextField($elementXpath, "Prefix", "Prefix Sample Value");
        $this->_helper->fillTextField($elementXpath, "First Name", $TestID);
        $this->_helper->fillTextField($elementXpath,"Last Name", "Lname Sample Value");
        $this->_helper->fillTextField($elementXpath,"Middle Name", "Mname Sample Value");
        $this->_helper->fillTextField($elementXpath,"Suffix","Suffix Sample Value");
        $this->_helper->fillTextField($elementXpath,"Company","Company Sample Value");
        $this->_helper->fillAddressLines($elementXpath,"Street Address Sample L1", "Street Address Sample L2");
        $this->_helper->fillTextField($elementXpath,"City","City Sample Value");
        $this->_helper->selectCountry($elementXpath,"United States");
        $this->_helper->selectState($elementXpath,"California");
        $this->_helper->fillTextField($elementXpath,"Zip","Zip Sample Value");
        $this->_helper->fillTextField($elementXpath,"Telephone","Telephone Sample Value");
        $this->_helper->fillTextField($elementXpath,"Fax","Fax Sample Value");
        // Fill New Address End

        // Specify Default Billing Address
        if ($isBilling) {
            $this->click(AddrManagePanel."//label[contains(text(),'Billing')]");
        }
        if ($isShipping) {
            $this->click(AddrManagePanel."//label[contains(text(),'Shipping')]");
        }

        //Save Customer
        $this->_helper->doAdminSaveCustomer();
    }

    function verifyAddress($CustID, $TestID, $isBilling, $isShipping) {
        // Verify Section Start
        $this->_helper-> doOpenCustomer($CustID);
        // Open Address Tab
        $this->click("//a[@id='customer_info_tabs_addresses']/span");
        $AddrManagePanel = "//table[contains(@class,'form-edit')]//td[contains(@class,'address-list')]//ul[contains(@id,'address_list')]";
        $this->click($AddrManagePanel."//li[contains(address, '".$TestID." Lname')]//img[@alt='Edit address']");

        $elementXpath = $this->_helper->getUiElement('EditAddrFieldsTable');

        $this->_helper->checkTextField($elementXpath, "Prefix","Prefix Sample Value");
        $this->_helper->checkTextField($elementXpath,"First Name",$TestID);
        $this->_helper->checkTextField($elementXpath,"Last Name", "Lname Sample Value");
        $this->_helper->checkTextField($elementXpath, "Middle Name", "Mname Sample Value");
        $this->_helper->checkTextField($elementXpath,"Suffix","Suffix Sample Value");
        $this->_helper->checkTextField($elementXpath,"Company","Company Sample Value");
        $this->_helper->checkAddressLines($elementXpath,"Street Address Sample L1", "Street Address Sample L2");
        $this->_helper->checkTextField($elementXpath,"City","City Sample Value");
        $this->_helper->checkCountry($elementXpath,"United States");
        $this->_helper->checkState($elementXpath,"California");
        $this->_helper->checkTextField($elementXpath,"Zip","Zip Sample Value");
        $this->_helper->checkTextField($elementXpath,"Telephone","Telephone Sample Value");
        $this->_helper->checkTextField($elementXpath,"Fax","Fax Sample Value");
        // Verify Section End

        // Check Default Billing and Shipping Address Options
        $this->_helper->checkIsDefaultState($this->_helper->getUiElement('AddrManagePanel'), $isBilling, $isShipping);
    }

    function setUp() {
        parent::setUp();
    }

    function testAddNewCuAddress() {
        $this->_helper->doLogin( $this->_baseurl, $this->_username, $this->_password);
        if ($this->_helper->delAddresses(AddrManagePanel, $this->_customerId, $this->_testId."1")) {
            if ($this->addAddress($this->_customerId, $this->_testId."1", false, false)) {
                $this->verifyAddress($this->_customerId, $this->_testId."1", false, false);
            }
        }
    }

    function testAddNewCuAddress_Billing() {
        $this->_helper->doLogin($this->_baseUrl, $this->_username, $this->_password);
        if ($this->_helper->delAddresses(AddrManagePanel, $this->_customerId, $this->_testId."2")) {
            if ($this->addAddress($this->_customerId, $this->_testId."2", true, false)) {
                $this->verifyAddress($this->_customerId, $this->_testId."2", true, false);
            }
        }
    }

    function testAddNewCuAddress_Shipping() {
        $this->_helper->doLogin( $this->_baseUrl, $this->_username, $this->_password);
        if ($this->_helper->delAddresses(AddrManagePanel, $this->_customerId, $this->_testId."3")) {
            if ($this->addAddress($this->_customerId, $this->_testId."3", false, true)) {
                $this->verifyAddress($this->_customerId, $this->_testId."3", false, true);
            }
        }
    }

    function testAddNewCuAddress_ShippingBilling() {
        $this->_helper->adminhelper->doLogin( $this->_baseUrl, $this->_username, $this->_password);
        if ($this->_helper->delAddresses(AddrManagePanel, $this->_customerId, $this->_testId."4")) {
            if (   $this->addAddress($this->_customerId, $this->_testId."4", true, true)) {
                $this->verifyAddress($this->_customerId, $this->_testId."4", true, true);
            }
        }
    }

}
