<?php

class Admin_Category_addsub extends TestCaseAbstract
{

    /**
     * Setup procedure.
     * Initializes model and loads configuration
     */
    function setUp() {
        $this->model = $this->getModel('admin/category');
        $this->setUiNamespace();
    }


    /**
     * Test addion new subcategory to the rootCategory
     *
     */
    function testSubCategoryCreation() {
        // Test Flow
        if ($this->model->doLogin()) {
            $this->model->doDeleteSubCategory();
            $this->model->doAddSubCategory();
        }
    }
}