<?php

class Admin_Customer_CustomerAddAllFields extends TestCaseAbstract {

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
    function testCustomerCreateWithAllFields()
    {
        $Data = array(
            'associate_website'         => 'SmokeTestSite',
            'customer_group'            => 'General',
            'prefix'                    => '<prefix>',
            'first_name'                => 'fromAdmin',
            'middle_name'               => '<middle_name>',
            'last_name'                 => '(With Address)',
            'suffix'                    => '<suffix>',
            'email'                     => 'test_customer_2@magento.com',
            'date_of_birth'             => '10/10/12',
            'tax_vat_number'            => '12345',
            'gender'                    => 'Male',
            'send_welcome_email'        => 'Yes',
            'send_welcome_email_from'   => 'SmokeTestStoreView',
            'password'                  => '123123q',
            //Address Data
            'address_prefix'            => '<prefix(address)_1>',
            'address_first_name'        => 'FName(address)_1',
            'address_middle_name'       => '<middleName(address)_1>',
            'address_last_name'         => 'LName(address)_1',
            'address_suffix'            => '<suffix(address)_1>',
            'address_company'           => 'Magento',
            'address_strreet'           => '11832 W. Pico Blvd',
            'address_city'              => 'Los Angeles',
            'address_country'           => 'United States',
            'address_state'             => 'California',
            'address_zip_code'          => '90064',
            'address_tel'               => '(310) 954-8012',
            'address_fax'               => '(310) 919-1189',
            'use_as_default'            => array('billing', 'shipping')
        );
        if ($this->model->doLogin()) {
            $this->model->doCreateCustomer($Data);
        }
    }

}