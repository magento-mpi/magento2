<?php

class Admin_Category_deleteroot extends TestCaseAbstract
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
     * Delete root category
     *
     */
    function testRootCategoryDeletion() {
        // Test Flow
        if ($this->model->doLogin()) {
            $this->model->doDeleteRootCategory();
        }
    }
}