<?php

class Admin_Category_Add extends TestCaseAbstract
{

    /**
     * Setup procedure.
     * Initializes model and loads configuration
     */
    function setUp() {
        $this->model = $this->getModel('admin/category/category');
        $this->setUiNamespace();
    }


    /**
     * Addion new root category to the $StoreView store view test.
     *
     */
    function testRootCategoryCreation() {
        // Test Flow
        if ($this->model->doLogin()) {
            $this->model->doAddRootCategory();
        }
    }
}