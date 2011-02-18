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
        $setData = Core::getEnvConfig('backend/attribute_set');
        /*$setData = array(
            'search_set_name' => 'smoke_attrSet'
        );*/
        if ($this->model->doLogin()) {
            $this->model->doDeleteAtrSet($setData);
        }
    }

}