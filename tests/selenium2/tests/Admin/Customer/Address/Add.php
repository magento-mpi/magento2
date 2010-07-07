<?php

class Admin_Customer_Address_Add extends Test_Admin_Customer_Abstract
{

    /**
     * Add new address to the customer @CustID
     * @param subTestID - will be placed to the  First Name field
     * @param boolean isBilling - if set, new address will be dafault billing address
     * @param boolean isShipping - if set, new address will be dafault shipping address
     *
     */
    function addAddress($subTestID, $isBilling, $isShipping)
    {
        $this->_helper->doOpenCustomer($this->_customerId);
        // Open Address Tab
        $this->click("//a[@id='customer_info_tabs_addresses']/span");
        $this->click("add_address_button");
        
        // Fill New Address
        $elementXpath = $this->_helper->getUiElement('NewAddrFieldsTable');
        
        $this->_helper->fillTextField($elementXpath, "Prefix", "Prefix Sample Value");
        $this->_helper->fillTextField($elementXpath, "First Name", $subTestID);
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
            $this->click($this->_helper->getUiElement("AddrManagePanel")."//label[contains(text(),'Billing')]");
        }
        if ($isShipping) {
            $this->click($this->_helper->getUiElement("AddrManagePanel")."//label[contains(text(),'Shipping')]");
        }

        //Save Customer
        $this->_helper->doAdminSaveCustomer();
    }

    /**
     * Check values of added address.
     * @param CustID - ID customer
     * @param TestID - address with TestID in the First Name field will be used
     * @param boolean isBilling - if set, address will be checked as dafault billing address
     * @param boolean isShipping - if set, address will be checked as dafault shipping address
     *
     */
    function verifyAddress($subTestID, $isBilling, $isShipping)
    {
        // Verify Section Start
        $this->_helper-> doOpenCustomer( $this->$customerID);
        // Open Address Tab
        $this->click("//a[@id='customer_info_tabs_addresses']/span");
       
        $this->click($this->_helper->getUiElement("AddrManagePanel")."//li[contains(address, '". $subTestID." Lname')]//img[@alt='Edit address']");

        $elementXpath = $this->_helper->getUiElement('EditAddrFieldsTable');

        $this->_helper->checkTextField($elementXpath, "Prefix","Prefix Sample Value");
        $this->_helper->checkTextField($elementXpath,"First Name", $subTestID);
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

    /**
     * Run apecfic actions before every test
     *
     */
    function setUp() {
        parent::setUp();
    }

    /**
     * Run single test: Login, Delete previously added address if exists, add address
     * @param CustID - ID customer
     * @param TestID - address with TestID in the First Name field will be used
     * @param boolean isBilling - if set, address will be checked as dafault billing address
     * @param boolean isShipping - if set, address will be checked as dafault shipping address
     *
     */
    function runSingleTest($subTestID, $isBilling, $isShipping) {
        echo ("\nStarting ".$subTestID." test \n");
        $this->_helper->doLogin( $this->_baseUrl, $this->_userName, $this->_password);
        if ($this->_helper->delAddresses($this->_helper->getUiElement("AddrManagePanel"), $this->_customerId, $this->_testId.$subTestID)) {
            if ($this->addAddress( $this->_testId.$subTestID, $isBilling, $isShipping)) {
                $this->verifyAddress( $this->_testId.$subTestID, $isBilling, $isShipping);
            }
        }
    }


    /**
     * Test adding of ordinal address to the customer
     *
     */
    function testAddNewCuAddress() {
        $this->runSingleTest("1",false, false);
    }

    /**
     * Test adding of default billing address to the customer
     *
     */
    function testAddNewCuAddress_Billing() {
        $this->runSingleTest("2",true, false);
    }

    /**
     * Test adding of default shipping address to the customer
     *
     */
    function testAddNewCuAddress_Shipping() {
        $this->runSingleTest("3",false, true);
    }

    /**
     * Test adding of default billing and shipping address to the customer
     *
     */
    function testAddNewCuAddress_ShippingBilling() {
        $this->runSingleTest("4",true, true);
    }

}
