<?php

define ( "NewAddrFieldsTable",  "//div[@id='address_form_container']//div[contains(@id,'form_new_item')     and not(contains(@style,'display: none'))]//table[contains(@class,'form-list')]/tbody");
define ( "EditAddrFieldsTable", "//div[@id='address_form_container']//div[contains(@id,'form_address_item') and not(contains(@style,'display: none'))]//table[contains(@class,'form-list')]/tbody");
define ( "AddrManagePanel", "//table[contains(@class,'form-edit')]//td[contains(@class,'address-list')]//ul[contains(@id,'address_list')]//li[contains(@class,'on')]");

class Admin_Customer_Address_Add extends Test_Admin_Customer_Abstract
{
    protected $_admincustomeraddresshelper;


    function addAddress($CustID, $TestID, $isBilling, $isShipping) {
        $this->_admincustomeraddresshelper-> doOpenCustomer($CustID);
        // Open Address Tab
        $this->click("//a[@id='customer_info_tabs_addresses']/span");
        $this->click("add_address_button");
        // Fill New Address
        $this->_admincustomeraddresshelper->fillTextField(NewAddrFieldsTable,"Prefix","Prefix Sample Value");
        $this->_admincustomeraddresshelper->fillTextField(NewAddrFieldsTable,"First Name",$TestID);
        $this->_admincustomeraddresshelper->fillTextField(NewAddrFieldsTable,"Last Name", "Lname Sample Value");
        $this->_admincustomeraddresshelper->fillTextField(NewAddrFieldsTable,"Middle Name", "Mname Sample Value");
        $this->_admincustomeraddresshelper->fillTextField(NewAddrFieldsTable,"Suffix","Suffix Sample Value");
        $this->_admincustomeraddresshelper->fillTextField(NewAddrFieldsTable,"Company","Company Sample Value");
        $this->_admincustomeraddresshelper->fillAddressLines(NewAddrFieldsTable,"Street Address Sample L1", "Street Address Sample L2");
        $this->_admincustomeraddresshelper->fillTextField(NewAddrFieldsTable,"City","City Sample Value");
        $this->_admincustomeraddresshelper->selectCountry(NewAddrFieldsTable,"United States");
        $this->_admincustomeraddresshelper->selectState(NewAddrFieldsTable,"California");
        $this->_admincustomeraddresshelper->fillTextField(NewAddrFieldsTable,"Zip","Zip Sample Value");
        $this->_admincustomeraddresshelper->fillTextField(NewAddrFieldsTable,"Telephone","Telephone Sample Value");
        $this->_admincustomeraddresshelper->fillTextField(NewAddrFieldsTable,"Fax","Fax Sample Value");
        // Fill New Address End

        // Specify Default Billing Address
        if ($isBilling) {
            $this->click(AddrManagePanel."//label[contains(text(),'Billing')]");
        }
        if ($isShipping) {
            $this->click(AddrManagePanel."//label[contains(text(),'Shipping')]");
        }

        //Save Customer
        $this->_admincustomeraddresshelper->doAdminSaveCustomer();
    }

    function verifyAddress($CustID, $TestID, $isBilling, $isShipping) {
        // Verify Section Start
        $this->_admincustomeraddresshelper-> doOpenCustomer($CustID);
        // Open Address Tab
        $this->click("//a[@id='customer_info_tabs_addresses']/span");
        $AddrManagePanel = "//table[contains(@class,'form-edit')]//td[contains(@class,'address-list')]//ul[contains(@id,'address_list')]";
        $this->click($AddrManagePanel."//li[contains(address, '".$TestID." Lname')]//img[@alt='Edit address']");

        $this->_admincustomeraddresshelper->checkTextField(EditAddrFieldsTable, "Prefix","Prefix Sample Value");
        $this->_admincustomeraddresshelper->checkTextField(EditAddrFieldsTable,"First Name",$TestID);
        $this->_admincustomeraddresshelper->checkTextField(EditAddrFieldsTable,"Last Name", "Lname Sample Value");
        $this->_admincustomeraddresshelper->checkTextField(EditAddrFieldsTable, "Middle Name", "Mname Sample Value");
        $this->_admincustomeraddresshelper->checkTextField(EditAddrFieldsTable,"Suffix","Suffix Sample Value");
        $this->_admincustomeraddresshelper->checkTextField(EditAddrFieldsTable,"Company","Company Sample Value");
        $this->_admincustomeraddresshelper->checkAddressLines(EditAddrFieldsTable,"Street Address Sample L1", "Street Address Sample L2");
        $this->_admincustomeraddresshelper->checkTextField(EditAddrFieldsTable,"City","City Sample Value");
        $this->_admincustomeraddresshelper->checkCountry(EditAddrFieldsTable,"United States");
        $this->_admincustomeraddresshelper->checkState(EditAddrFieldsTable,"California");
        $this->_admincustomeraddresshelper->checkTextField(EditAddrFieldsTable,"Zip","Zip Sample Value");
        $this->_admincustomeraddresshelper->checkTextField(EditAddrFieldsTable,"Telephone","Telephone Sample Value");
        $this->_admincustomeraddresshelper->checkTextField(EditAddrFieldsTable,"Fax","Fax Sample Value");
        // Verify Section End

        // Check Default Billing and Shipping Address Options
        $this->_admincustomeraddresshelper->checkIsDefaultState(AddrManagePanel, $isBilling, $isShipping);
    }

    function setUp() {

        $this->setBrowser("*chrome");
        $this->setBrowserUrl("http://kq.varien.com/");
        $this->_admincustomeraddresshelper = new AdminCustomerAddressHelper($this);

        // Get test parameters....
        //
        $this->_baseurl = "http://kq.varien.com/enterprise/1.8.0.0/index.php/control/index/";
        $this->_username = "admin";
        $this->_password = "123123q";
        $this->_custid = "102";
        $this->_testid = "CU_ADDR_ADD_0";
        
    }

    function testAddNewCuAddress() {
        $this->_admincustomeraddresshelper->adminhelper->doLogin( $this->_baseurl, $this->_username, $this->_password);
        if ($this->_admincustomeraddresshelper->delAddresses(AddrManagePanel, $this->_custid, $this->_testid."1")) {
            if ($this->addAddress($this->_custid, $this->_testid."1", false, false)) {
                $this->verifyAddress($this->_custid, $this->_testid."1", false, false);
            }
        }
    }

    function testAddNewCuAddress_Billing() {
        $this->_admincustomeraddresshelper->adminhelper->doLogin( $this->_baseurl, $this->_username, $this->_password);
        if ($this->_admincustomeraddresshelper->delAddresses(AddrManagePanel, $this->_custid, $this->_testid."2")) {
            if ($this->addAddress($this->_custid, $this->_testid."2", true, false)) {
                $this->verifyAddress($this->_custid, $this->_testid."2", true, false);
            }
        }
    }

    function testAddNewCuAddress_Shipping() {
        $this->_admincustomeraddresshelper->adminhelper->doLogin( $this->_baseurl, $this->_username, $this->_password);
        if ($this->_admincustomeraddresshelper->delAddresses(AddrManagePanel, $this->_custid, $this->_testid."3")) {
            if ($this->addAddress($this->_custid, $this->_testid."3", false, true)) {
                $this->verifyAddress($this->_custid, $this->_testid."3", false, true);
            }
        }
    }

    function testAddNewCuAddress_ShippingBilling() {
        $this->_admincustomeraddresshelper->adminhelper->doLogin( $this->_baseurl, $this->_username, $this->_password);
        if ($this->_admincustomeraddresshelper->delAddresses(AddrManagePanel, $this->_custid, $this->_testid."4")) {
            if (   $this->addAddress($this->_custid, $this->_testid."4", true, true)) {
                $this->verifyAddress($this->_custid, $this->_testid."4", true, true);
            }
        }
    }

}
?>