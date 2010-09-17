<?php

class Frontend_Checkout_Guest extends TestCaseAbstract
{
    /**
     * Setup procedure.
     * Initializes model and loads configuration
     */
    function setUp() {
        $this->model = $this->getModel('frontend/checkout');
        $this->setUiNamespace();
    }

    /**
     * Test frontend checkout
     */
    function testCheckoutGuest()
    {
        $this->model->doCreate();
    }
}
