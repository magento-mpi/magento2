<?php
class Varien_Data_FormTest extends PHPUnit_Framework_TestCase
{
    /**
     * Form object
     *
     * @var Varien_Data_Form
     */
    protected $_form;
    
    protected function setUp()
    {
        $attributes = array(
            'id'    => 'test_form',
            'name'  => 'test_form',
            'method'=> 'post',
            'action'=> '/',
        );
        $this->_form = new Varien_Data_Form($attributes);
    }

    public function testAddField()
    {
        //$this->_form->addField();
    }
    
    public function testAddFieldset()
    {
        $fieldset = $this->_form->addFieldset('fieldset1', array('label'=>'Fieldset 1'));
        $column1 = $fieldset->addColumn('column1', array('width'=>'300'));
        $column1->addField('element1', 'text', array('name'=>'f_field'));
        $column2 = $fieldset->addColumn('column2', array('width'=>'300'));
    }
    
    public function testAddColumn()
    {
    }
}

