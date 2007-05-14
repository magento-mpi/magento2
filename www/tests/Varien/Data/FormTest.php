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
        // adding fields
        $this->_form->addField('hidden_field', 'hidden', array('name'=>'field[hidden]', 'value'=>'22'));
        $this->_form->addField('checkbox_field', 'checkbox', array('name'=>'field[checkbox]', 'value'=>'1'));
        $this->_form->addField('file_field', 'file', array('name'=>'field[file]', 'class'=>'file-field'));
        $this->_form->addField('radio_field', 'radio', array('name'=>'field[radio]', 'value'=>'test')); 
        $this->_form->addField('select_field', 'select', array('name'=>'field[select]', 'value'=>'s'));
        
        $this->_form->getElement('select_field')->setValues(array(''));
        
        // set fields values
        $arrElementsValues = array(
            'hidden_field'  => 'hidden',
            'checkbox_field'=> 'check',
            'radio_field'   => 'radio',
            'select_field'  => 'val'
        );
        $this->_form->setValues($arrElementsValues);
    }
    
    public function testAddFieldset()
    {
        $fieldset = $this->_form->addFieldset('fieldset1', array('label'=>'Fieldset 1'));
        $column1 = $fieldset->addColumn('column1', array('width'=>'300'));
        $column1->addField('checkbox_field', 'checkbox', array('name'=>'field[checkbox]', 'value'=>'1'));
        $column1->addField('file_field', 'file', array('name'=>'field[file]', 'class'=>'file-field'));
        $column1->addField('radio_field', 'radio', array('name'=>'field[radio]', 'value'=>'test')); 

        $column2 = $fieldset->addColumn('column2', array('width'=>'300'));
        $column2->addField('checkbox_field2', 'checkbox', array('name'=>'field[checkbox2]', 'value'=>'1'));
        $column2->addField('file_field2', 'file', array('name'=>'field[file2]', 'class'=>'file-field'));
        $column2->addField('radio_field2', 'radio', array('name'=>'field[radio2]', 'value'=>'test')); 
    }
    
}

