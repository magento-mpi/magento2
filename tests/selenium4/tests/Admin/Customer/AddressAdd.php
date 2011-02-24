<?php

class Admin_Customer_AddressAdd extends TestCaseAbstract {

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
     * Add Address For Exist Customer
     */
    function testAddAddressForExistCustomer()
    {
        $Data = array(
            //Customer Data
            'search_user_name'          => '<prefix> fromAdmin <middle_name> (With Address) <suffix>',
            'search_user_email'         => 'test_customer_2@magento.com',
            //Address Data
            'address_prefix'            => '<prefix(address)_2>',
            'address_first_name'        => 'FName(address)_2',
            'address_middle_name'       => '<middleName(address)_2>',
            'address_last_name'         => 'LName(address)_2',
            'address_suffix'            => '<suffix(address)_2>',
            'address_company'           => 'Magento',
            'address_strreet'           => '11832 W. Pico Blvd',
            'address_city'              => 'Los Angeles',
            'address_country'           => 'United States',
            'address_state'             => 'California',
            'address_zip_code'          => '90064',
            'address_tel'               => '(310) 954-8012',
            'address_fax'               => '(310) 919-1189',
            //'use_as_default'            => array('billing', 'shipping')
        );
        if ($this->model->doLogin()) {
            $this->model->doAddAddress($Data);
        }
    }

}