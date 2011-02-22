<?php

class Admin_Category_addroot extends TestCaseAbstract {

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
     * Addion new root category to the $StoreView store view test.
     *
     */
    function testRootCategoryCreate()
    {
        // Test Flow
        $params = array(
            'category_name' => Core::getEnvConfig('backend/categories/root_name'),
            'category_is_active' => 'Yes'
        );
        if ($this->model->doLogin()) {
            $this->model->navigate("Catalog/Categories/Manage Categories");
            if ($this->model->doSelectCategory(NULL)) {
                $this->model->doCreateCategory($params);
            }
        }
    }

}