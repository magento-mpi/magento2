<?php

class Admin_AttributeSet_AddSet extends TestCaseAbstract {

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
     * Test addition new Attribute Set
     */
    function testAttributeSetCreation()
    {
        $setData = Core::getEnvConfig('backend/attribute_set');
        if ($this->model->doLogin()) {
            $this->model->navigate('Catalog/Attributes/Manage Attribute Sets');
            $this->model->doCreateAtrSet($setData);
        }
    }

}