<?php

class Admin_AttributeSet_DeleteSet extends TestCaseAbstract {

    /**
     * Setup procedure.
     * Initializes model and loads configuration
     */
    function setUp()
    {
        $this->model = $this->getModel('admin/attributeset');
        $this->setUiNamespace();
    }

    /**
     * Test deletion Attribute Set
     */
    function testAttributeSetDeletion()
    {
        $setData = array(
            'search_set_name' =>Core::getEnvConfig('backend/attribute_set/set_name')
        );
        if ($this->model->doLogin()) {
            $this->model->navigate('Catalog/Attributes/Manage Attribute Sets');
            $this->model->doDeleteAtrSet($setData);
        }
    }

}