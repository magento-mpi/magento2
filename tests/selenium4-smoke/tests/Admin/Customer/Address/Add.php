<?php

class Admin_Customer_Address_Add extends TestCaseAbstract
{
    /**
     * Setup procedure.
     * Must be overriden in the children having any additional code prepended with parent::setUp();
     */
    function setUp() {
        $this->model = $this->getModel('admin/customer/address');
        $this->model->loadConfigData();
    }


    /**
     * Test adding of ordinal address to the customer
     *
     */
    public function testAddNewCuAddress()
    {
        $this->setUiNamespace('dfdfd/dfdfd/dfdfd');
        $a = $this->getUiElement('/asd/asd');
        $this->model->probeAddressCreation('1', false, false);
    }

    /**
     * Test adding of default billing address to the customer
     *
     */
    public function testAddNewCuAddress_Billing()
    {
        $this->model->probeAddressCreation('2', true, false);
    }

    /**
     * Test adding of default shipping address to the customer
     *
     */
    public function testAddNewCuAddress_Shipping()
    {
        $this->model->probeAddressCreation('3', false, true);
    }

    /**
     * Test adding of default billing and shipping address to the customer
     *
     */
    public function testAddNewCuAddress_ShippingBilling()
    {
        $this->model->probeAddressCreation('4', true, true);
    }
}
