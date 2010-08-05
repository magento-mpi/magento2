<?php

class Admin_Customer_Address_Add extends Test_Admin_Customer_Address_Abstract
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
        $this->doOpenCustomer($this->_customerId);
        // Open Address Tab
        $this->click("//a[@id='customer_info_tabs_addresses']/span");
        $this->click("add_address_button");
        
        // Fill New Address
        $elementXpath = $this->getUiElement("admin/customer/address/newaddrfieldstable");
        
        $this->fillTextField($elementXpath, "Prefix", "Prefix Sample Value");
        $this->fillTextField($elementXpath, "First Name", $subTestID);
        $this->fillTextField($elementXpath,"Last Name", "Lname Sample Value");
        $this->fillTextField($elementXpath,"Middle Name", "Mname Sample Value");
        $this->fillTextField($elementXpath,"Suffix","Suffix Sample Value");
        $this->fillTextField($elementXpath,"Company","Company Sample Value");
        $this->fillAddressLines($elementXpath,"Street Address Sample L1", "Street Address Sample L2");
        $this->fillTextField($elementXpath,"City","City Sample Value");
        $this->selectCountry($elementXpath,"United States");
        $this->selectState($elementXpath,"California");
        $this->fillTextField($elementXpath,"Zip","Zip Sample Value");
        $this->fillTextField($elementXpath,"Telephone","Telephone Sample Value");
        $this->fillTextField($elementXpath,"Fax","Fax Sample Value");
        // Fill New Address End

        // Specify Default Billing Address
        if ($isBilling) {
            $this->click($this->getUiElement("AddrManagePanel")."//label[contains(text(),'Billing')]");
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
    function verifyAddress($subTestID, $isBilling, $isShipping)
    {
        // Verify Section Start
        $this-> doOpenCustomer( $this->$customerID);
        // Open Address Tab
        $this->click("//a[@id='customer_info_tabs_addresses']/span");
       
        $this->click($this->getUiElement("AddrManagePanel")."//li[contains(address, '". $subTestID." Lname')]//img[@alt='Edit address']");

        $elementXpath = $this->getUiElement("admin/customer/address/editdfields");

        $this->checkTextField($elementXpath, "Prefix","Prefix Sample Value");
        $this->checkTextField($elementXpath,"First Name", $subTestID);
        $this->checkTextField($elementXpath,"Last Name", "Lname Sample Value");
        $this->checkTextField($elementXpath, "Middle Name", "Mname Sample Value");
        $this->checkTextField($elementXpath,"Suffix","Suffix Sample Value");
        $this->checkTextField($elementXpath,"Company","Company Sample Value");
        $this->checkAddressLines($elementXpath,"Street Address Sample L1", "Street Address Sample L2");
        $this->checkTextField($elementXpath,"City","City Sample Value");
        $this->checkCountry($elementXpath,"United States");
        $this->checkState($elementXpath,"California");
        $this->checkTextField($elementXpath,"Zip","Zip Sample Value");
        $this->checkTextField($elementXpath,"Telephone","Telephone Sample Value");
        $this->checkTextField($elementXpath,"Fax","Fax Sample Value");
        // Verify Section End

        // Check Default Billing and Shipping Address Options
        $this->checkIsDefaultState($this->getUiElement('AddrManagePanel'), $isBilling, $isShipping);
    }

    /**
     * Setup procedure.
     * Must be overriden in the children having any additional code prepended with parent::setUp();
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
        $this->doLogin( $this->_baseUrl, $this->_userName, $this->_password);
        if ($this->delAddresses( $this->_testId.$subTestID)) {
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
