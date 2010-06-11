<?php

define( "ABSPATH" ,dirname(__FILE__));
define ( "NewAddrFieldsTable",  "//div[@id='address_form_container']//div[contains(@id,'form_new_item')     and not(contains(@style,'display: none'))]//table[contains(@class,'form-list')]/tbody");
define ( "EditAddrFieldsTable", "//div[@id='address_form_container']//div[contains(@id,'form_address_item') and not(contains(@style,'display: none'))]//table[contains(@class,'form-list')]/tbody");
define ( "AddrManagePanelActive", "//table[contains(@class,'form-edit')]//td[contains(@class,'address-list')]//ul[contains(@id,'address_list')]//li[contains(@class,'on')]");
define ( "AddrManagePanel", "//table[contains(@class,'form-edit')]//td[contains(@class,'address-list')]");

class cu_del_address extends PHPUnit_Extensions_SeleniumTestCase {
    protected $_admincustomeraddresshelper;

    // Test Data
    protected $_baseurl;
    protected $_username;
    protected $_password;
    protected $_custid;
    protected $_testid;

    function setVerificationErrors($error) {
        array_push($this->verificationErrors, $error);
    }

    function checkAndDelAddress($AddrManagePanel, $CustID, $TestID) {
        //$AddrManagePanel = "//table[contains(@class,'form-edit')]//td[contains(@class,'address-list')]//ul[contains(@id,'address_list')]//li[contains(address,'".$TestID."')]";
        if ($this->_admincustomeraddresshelper->doOpenCustomer($CustID)) {
            if ($this->isElementPresent(($AddrManagePanel."//ul[contains(@id,'address_list')]//li[contains(address,'".$TestID."')]"))) {
                // Remove Test Addresses
                while ($this->isElementPresent("//table[contains(@class,'form-edit')]//td[contains(@class,'address-list')]//ul[contains(@id,'address_list')]//li[contains(address,'".$TestID."')]//img[@alt='Remove address']")) {
                    $this->click("//table[contains(@class,'form-edit')]//td[contains(@class,'address-list')]//ul[contains(@id,'address_list')]//li[contains(address,'".$TestID."')]//img[@alt='Remove address']");
                    $this->getConfirmation();
                };
                $this->_admincustomeraddresshelper->doAdminSaveCustomer();
                return true;
            } else {
                $this->setVerificationErrors ("Address ".$TestID." doesn't exists");
                return false;
            }
        } else {
            return false;
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

    function testDelCuAddress() {
        $this->_admincustomeraddresshelper->adminhelper->doLogin( $this->_baseurl, $this->_username, $this->_password);
        if ($this->checkAndDelAddress(AddrManagePanel,$this->_custid,$this->_testid."1")) {
            if ($this->_admincustomeraddresshelper -> isCustomerAddressPresent(AddrManagePanel,$this->_custid,$this->_testid."1")) {
                $this->setVerificationErrors ("Address ".$this->_testid."1"." still exists");
            }
        }
    }

    function testDelCuAddress_Billing() {
        $this->_admincustomeraddresshelper->adminhelper->doLogin( $this->_baseurl, $this->_username, $this->_password);
        if ($this->checkAndDelAddress(AddrManagePanel,$this->_custid,$this->_testid."2")) {
            if ($this->_admincustomeraddresshelper -> isCustomerAddressPresent(AddrManagePanel,$this->_custid,$this->_testid."2")) {
                $this->setVerificationErrors ("Address ".$this->_testid."2"." still exists");
            }
        }
    }

    function testDelCuAddress_Shipping() {
        $this->_admincustomeraddresshelper->adminhelper->doLogin( $this->_baseurl, $this->_username, $this->_password);
        if ($this->checkAndDelAddress(AddrManagePanel,$this->_custid,$this->_testid."3")) {
            if ($this->_admincustomeraddresshelper -> isCustomerAddressPresent(AddrManagePanel,$this->_custid,$this->_testid."3")) {
                $this->setVerificationErrors ("Address ".$this->_testid."3"." still exists");
            }
        }
    }

    function testDelCuAddress_BillingShipping() {
        $this->_admincustomeraddresshelper->adminhelper->doLogin( $this->_baseurl, $this->_username, $this->_password);
        if ($this->checkAndDelAddress(AddrManagePanel,$this->_custid,$this->_testid."4")) {
            if ($this->_admincustomeraddresshelper -> isCustomerAddressPresent(AddrManagePanel,$this->_custid,$this->_testid."4")) {
                $this->setVerificationErrors ("Address ".$this->_testid."4"." still exists");
            }
        }
    }

}
?>