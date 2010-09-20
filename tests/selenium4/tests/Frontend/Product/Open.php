<?php

class Frontend_Product_Open extends TestCaseAbstract
{
    /**
     * Setup procedure.
     * Initializes model and loads configuration
     */
    function setUp() {
        $this->model = $this->getModel('frontend/product');
        $this->setUiNamespace();
    }

    /**
     * Test on frontend product page next values:
     */
    function testProduct()
    {
        //Test Flow
        $this->model->testProduct();
    }
}
