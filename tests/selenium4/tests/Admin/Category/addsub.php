<?php

class Admin_Category_addsub extends TestCaseAbstract {

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
     * Addion new sub category to the $StoreView store view test.
     *
     */
    function testSubCategoryCreate()
    {
        // Test Flow
        $params = array(
            'category_path' => Core::getEnvConfig('backend/categories/root_name'),
            'category_name' => Core::getEnvConfig('backend/categories/sub_category_name'),
            'category_is_active' => 'Yes',
            'category_is_anchor' => 'Yes'
        );
        if ($this->model->doLogin()) {
            $this->model->navigate("Catalog/Categories/Manage Categories");
            $isSelected = $this->model->doSelectCategory($params['category_path']);
            if ($isSelected) {
                $this->model->doCreateCategory($params);
            }
        }
    }

}