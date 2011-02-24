<?php

class Admin_Customer_CustomerAddRequiredFields extends TestCaseAbstract {

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
     * test Create customer
     */
    function testCustomerCreateWithRequiredFields()
    {
        $Data = array(
            'associate_website'     => 'SmokeTestSite',
            'first_name'            => 'fromAdmin',
            'last_name'             => '(Without Address)',
            'email'                 => 'test_customer_1@magento.com',
            'password'              => '123123q',
        );
        if ($this->model->doLogin()) {
            $this->model->doCreateCustomer($Data);
        }
    }

}