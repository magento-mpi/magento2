<?php

class Admin_Category_deleteroot extends TestCaseAbstract {

    /**
     * Setup procedure.
     * Initializes model and loads configuration
     */
    function setUp()
    {
        $this->model = $this->getModel('admin/category');
        $this->setUiNamespace();
    }

    /**
     * Deletion root category
     *
     */
    function testDeleteRootCategory()
    {
        // Test Flow
        $categoryName = Core::getEnvConfig('backend/categories/root_name');

        if ($this->model->doLogin()) {
            $this->model->navigate("Catalog/Categories/Manage Categories");
            $this->model->doDeleteCategory($categoryName);
        }
    }

}