<?php

define( "ABSPATH" ,dirname(__FILE__));
define ( "NewAddrFieldsTable",  "//div[@id='address_form_container']//div[contains(@id,'form_new_item')     and not(contains(@style,'display: none'))]//table[contains(@class,'form-list')]/tbody");
define ( "EditAddrFieldsTable", "//div[@id='address_form_container']//div[contains(@id,'form_address_item') and not(contains(@style,'display: none'))]//table[contains(@class,'form-list')]/tbody");
define ( "AddrManagePanel", "//table[contains(@class,'form-edit')]//td[contains(@class,'address-list')]//ul[contains(@id,'address_list')]//li[contains(@class,'on')]");

class Example extends PHPUnit_Extensions_SeleniumTestCase {
    protected $_admincustomeraddresshelper;

    //  Place Test Data here...
    protected $_baseurl;
    protected $_username;
    protected $_password;
    protected $_custid;
    protected $_testid;


    function setVerificationErrors($error) {
        array_push($this->verificationErrors, $error);
    }

    function updateAddress($CustID, $TestID, $isBilling, $isShipping) {
        $this->_admincustomeraddresshelper-> doOpenCustomer($CustID);
        // Open Address Tab and get #TestID address to edit form
        $this->click("//a[@id='customer_info_tabs_addresses']/span");
        $AddrManagePanel = "//table[contains(@class,'form-edit')]//td[contains(@class,'address-list')]//ul[contains(@id,'address_list')]";
        if ($this->isElementPresent($AddrManagePanel."//li[contains(address, '".$TestID." Lname')]")) {
            // Address for Update exist
            $this->click($AddrManagePanel."//li[contains(address, '".$TestID." Lname')]//img[@alt='Edit address']");
            // Edit Address
            $this->_admincustomeraddresshelper->fillTextField(EditAddrFieldsTable,"Prefix","Prefix Changed Value");
            $this->_admincustomeraddresshelper->fillTextField(EditAddrFieldsTable,"First Name",$TestID." Changed");
            $this->_admincustomeraddresshelper->fillTextField(EditAddrFieldsTable,"Last Name", "Lname Changed Value");
            $this->_admincustomeraddresshelper->fillTextField(EditAddrFieldsTable,"Middle Name", "Mname Changed Value");
            $this->_admincustomeraddresshelper->fillTextField(EditAddrFieldsTable,"Suffix","Suffix Changed Value");
            $this->_admincustomeraddresshelper->fillTextField(EditAddrFieldsTable,"Company","Company Changed Value");
            $this->_admincustomeraddresshelper->fillAddressLines(EditAddrFieldsTable,"Street Address Changed L1", "Street Address Changed L2");
            $this->_admincustomeraddresshelper->fillTextField(EditAddrFieldsTable,"City","City Changed Value");
            $this->_admincustomeraddresshelper->selectCountry(EditAddrFieldsTable,"United Kingdom");
            $this->_admincustomeraddresshelper->fillTextField(EditAddrFieldsTable,"State/Province","London");
            $this->_admincustomeraddresshelper->fillTextField(EditAddrFieldsTable,"Zip","Zip Changed Value");
            $this->_admincustomeraddresshelper->fillTextField(EditAddrFieldsTable,"Telephone","Telephone Changed Value");
            $this->_admincustomeraddresshelper->fillTextField(EditAddrFieldsTable,"Fax","Fax Changed Value");
            // Edit Address End

            // Specify Default Billing Address
            if ($isBilling) {
                $this->click(AddrManagePanel."//label[contains(text(),'Billing')]");
            }
            if ($isShipping) {
                $this->click(AddrManagePanel."//label[contains(text(),'Shipping')]");
            }

            $this->_admincustomeraddresshelper->doAdminSaveCustomer();
            return true;
        } else {
            // Address for Update NOT exist
            // $this->fail("Customer ".$CustID." hasn't address with FirstName ==".$TestID."!");
            $this->setVerificationErrors("Customer with ID=".$CustID." hasn't address with FirstName = ".$TestID." !");
            return false;
        }

    }

    function verifyAddress($CustID, $TestID, $isBilling, $isShipping) {
        // Verify Section Start
        $this->_admincustomeraddresshelper-> doOpenCustomer($CustID);
        // Open Address Tab and get #TestID address to edit form
        $this->click("//a[@id='customer_info_tabs_addresses']/span");
        $AddrManagePanel = "//table[contains(@class,'form-edit')]//td[contains(@class,'address-list')]//ul[contains(@id,'address_list')]";
        if ($this->isElementPresent($AddrManagePanel."//li[contains(address, '".$TestID." Lname')]")) {
            // Address for Update exist
            $this->click($AddrManagePanel."//li[contains(address, '".$TestID." Lname')]//img[@alt='Edit address']");
            $this->_admincustomeraddresshelper->checkTextField(EditAddrFieldsTable, "Prefix","Prefix Changed Value");
            $this->_admincustomeraddresshelper->checkTextField(EditAddrFieldsTable,"First Name",$TestID);
            $this->_admincustomeraddresshelper->checkTextField(EditAddrFieldsTable,"Last Name", "Lname Changed Value");
            $this->_admincustomeraddresshelper->checkTextField(EditAddrFieldsTable, "Middle Name", "Mname Changed Value");
            $this->_admincustomeraddresshelper->checkTextField(EditAddrFieldsTable,"Suffix","Suffix Changed Value");
            $this->_admincustomeraddresshelper->checkTextField(EditAddrFieldsTable,"Company","Company Changed Value");
            $this->_admincustomeraddresshelper->checkAddressLines(EditAddrFieldsTable,"Street Address Changed L1", "Street Address Changed L2");
            $this->_admincustomeraddresshelper->checkTextField(EditAddrFieldsTable,"City","City Changed Value");
            $this->_admincustomeraddresshelper->checkCountry(EditAddrFieldsTable,"United Kingdom");
            $this->_admincustomeraddresshelper->checkTextField(EditAddrFieldsTable,"State/Province","London");
            $this->_admincustomeraddresshelper->checkTextField(EditAddrFieldsTable,"Zip","Zip Changed Value");
            $this->_admincustomeraddresshelper->checkTextField(EditAddrFieldsTable,"Telephone","Telephone Changed Value");
            $this->_admincustomeraddresshelper->checkTextField(EditAddrFieldsTable,"Fax","Fax Changed Value");
            // Verify Section End

            // Check Default Billing and Shipping Address Options
            $this->_admincustomeraddresshelper->checkIsDefaultState(AddrManagePanel, $isBilling, $isShipping);
        } else {
            // Address for Update NOT exist
        }
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

    function testUpdateCuAdress() {
        $this->_admincustomeraddresshelper->adminhelper->doLogin( $this->_baseurl, $this->_username, $this->_password);

        if ($this->updateAddress($this->_custid, $this->_testid."1", false, false)) {
            $this->verifyAddress($this->_custid, $this->_testid."1"." Changed", false, false);
        }
    }

    function testUpdateCuAdress_Billing() {
        $this->_admincustomeraddresshelper->adminhelper->doLogin( $this->_baseurl, $this->_username, $this->_password);

        if ($this->updateAddress($this->_custid, $this->_testid."2", true, false)) {
            $this->verifyAddress($this->_custid, $this->_testid."2"." Changed", true, false);
        }
    }

    function testUpdateCuAdress_Shipping() {
        $this->_admincustomeraddresshelper->adminhelper->doLogin( $this->_baseurl, $this->_username, $this->_password);

        if ($this->updateAddress($this->_custid, $this->_testid."3", false, true)) {
            $this->verifyAddress($this->_custid, $this->_testid."3"." Changed", false, true);
        }
    }

    function testUpdateCuAdress_BillingShipping() {
        $this->_admincustomeraddresshelper->adminhelper->doLogin( $this->_baseurl, $this->_username, $this->_password);

        if ($this->updateAddress($this->_custid, $this->_testid."4", true, true)) {
            $this->verifyAddress($this->_custid, $this->_testid."4"." Changed", true, true);
        }
    }

}
?>