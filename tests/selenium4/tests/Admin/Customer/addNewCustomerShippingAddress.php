<?php

class Admin_Customer_addNewCustomerShippingAddress extends TestCaseAbstract {

    /**
     * Setup procedure.
     * Initializes model and loads configuration
     */
    function setUp()
    {
        $this->model = $this->getModel('admin/customer');
        $this->setUiNamespace();
    }

    /**
     * Test creating customer
     * Verifying if customer with such id present:
     * 1) Yes
     *  verying if he have such address
     *      1.1) yes
     *       save customer
     *      1.2) no
     *       add such address and save customer
     * 2) No
     *  Create customer, add such address and save customer
     */
    function testNewCustomer()
    {
        $Data = array(
            'Last Name' => 'Last name 1',
            'First Name' => 'First name 1',
            'Email' => 'test19@test.com',
            'Telephone' => '54312312',
            'Zip' => '54312',
            'City' => 'Sheldonopolis'
        );
        $aData = array(
            'associate_to_website' => 'SmokeTestSite',
            'customer_group' => 'Wholesale',
            'country' => 'United States',
            'state' => 'Alaska',
            'password' => '123123q',
            'Last name' => 'Last name',
            'First_name' => 'First name',
            'Email' => 'test19@test.com',
            'CustID' => '17',
            'street_line_1' => 'Street Address Sample test',
            'street_line_2' => 'Street Address Sample test',
            'search' => array('Name' => 'First name'),
            'is billing' => false,
            'is shipping' => true,
        );
        $VerifyData = array(
            'country' => 'United States',
            'state' => 'California',
            'Last name' => 'Last name 2',
            'First_name' => 'First name 2',
            'street_line_1' => 'Street Address Sample L1',
            'street_line_2' => 'Street Address Sample L2',
            'is billing' => true,
            'is shipping' => false,
        );
        if ($this->model->doLogin()) {
            $this->model->addNewCustomer($Data, $aData, $VerifyData);
        }
    }

}