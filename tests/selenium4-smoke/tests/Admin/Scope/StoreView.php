<?php

class Admin_Scope_StoreView extends TestCaseAbstract
{

    /**
     * Setup procedure.
     * Initializes model and loads configuration
     */
    function setUp() {
        $this->model = $this->getModel('admin/scope/storeview');
        $this->setUiNamespace();
    }

    /**
     * Test storeView creation
     */
    function testStoreViewCreation()
    {
        $storeParamsArray = Core::getEnvConfig('backend/scope/store_view');
        $store4deleteParamsArray = Core::getEnvConfig('backend/scope/store_view_for_delete');

        if ($this->model->doLogin()) {
            //Create primary store view
            if (!$this->model->doOpen($storeParamsArray)) {
                $this->model->doCreate($storeParamsArray);
            };
            //Delete-Create for 4delete Store View
            $this->model->doDelete($store4deleteParamsArray);
            $this->model->doCreate($store4deleteParamsArray);
        }
    }
}
