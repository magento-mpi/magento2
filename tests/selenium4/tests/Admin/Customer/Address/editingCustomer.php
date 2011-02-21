<?php

class Admin_Customer_editingCustomer extends TestCaseAbstract {

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
            'First_Name' => 'First name 1',
            'Email' => 'test21@test.com',
            'Telephone' => '12345678',
            'Zip' => '12345',
            'City' => 'Sheldonopolis',
        );
        $aData = array(
            'associate_to_website' => 'SmokeTestSite', 
            'customer_group' => 'Wholesale', 
            'country' => 'United States', 
            'state' => 'California', 
            'password' => '123123q', 
            'Last name' => 'Last name', 
            'First_name' => 'First name',
            'Email' => 'test21@test.com',
            'street_line_1' => 'Street Address Sample L1', 
            'street_line_2' => 'Street Address Sample L2', 
            'search' => array('Name' => 'First name'),
            'is billing' => true,
            'is shipping' => false,
            'store_credit_website' => 'SmokeTestSite',
            'store_credit_balance' => '100',
            'Notify_email' => true,
            'store_credit_storeview' => 'SmokeTestStoreView',
            'store_credit_comment' => 'store credit comment',
            'reward_points_store' => 'SmokeTestStoreView',
            'reward points' => '99',
            'reward_points_comment' => 'reward points comment',
        );
        if ($this->model->doLogin()) {
            $this->model->editingCustomer($Data, $aData);
        }
    }

}