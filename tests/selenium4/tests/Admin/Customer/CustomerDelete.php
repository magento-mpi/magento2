<?php

class Admin_Customer_CustomerDelete extends TestCaseAbstract {

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
     * Delete Exist Customer
     */
    function testDeleteExistCustomer()
    {
        $Data = array(
            'search_user_name'      => '<prefix> fromAdmin <middle_name> (With Address) <suffix>',
            'search_user_email'     => 'test_customer_2@magento.com'
        );
        if ($this->model->doLogin()) {
            $this->model->doDelete($Data, 'customer');
        }
    }

}