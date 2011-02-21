<?php

class Admin_Customer_addNewCustomer extends TestCaseAbstract {

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
            'associate_to_website' => 'SmokeTestSite',
            'customer_group' => 'Wholesale',
            'password' => '123123q',
            'Last name' => 'Last name',
            'First_name' => 'First name',
            'Email' => 'test25@test.com',
        );
        if ($this->model->doLogin()) {
            $this->model->addNewCustomer($Data);
        }
    }

}