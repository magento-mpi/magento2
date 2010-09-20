<?php

class Frontend_Category_Open extends TestCaseAbstract
{
    /**
     * Setup procedure.
     * Initializes model and loads configuration
     */
    function setUp() {
        $this->model = $this->getModel('frontend/category');
        $this->setUiNamespace();
    }

    /**
     * Test frontend category page
     */
    function testCategory()
    {
        // Test Dara
        $paramArray = Core::getEnvConfig('backend/manage_categories');
        $cat = Core::getEnvConfig('backend/manage_categories/subcategoryname');
        //Test Flow
        if ($this->model->doOpen($paramArray)) {
            $this->model->testCategory();
        };
    }
}
