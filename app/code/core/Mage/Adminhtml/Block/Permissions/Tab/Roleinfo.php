<?php
/**
 * implementing now
 *
 */
class Mage_Adminhtml_Block_Permissions_Tab_Roleinfo extends Mage_Adminhtml_Block_Widget_Form
{
    public function __construct() 
    {
        parent::__construct();        
    }
    
    public function _beforeToHtml() {
    	$this->_initForm();
    	
    	return parent::_beforeToHtml();
    }
    
    protected function _initForm() {
        $form = new Varien_Data_Form();        
        
        $fieldset = $form->addFieldset('base_fieldset', array('legend'=>__('Role information')));
        
        $fieldset->addField('role_name', 'text', 
            array(
                'name'  => 'role_name',
                'label' => __('Role Title'),
                'id'    => 'role_name',
                'title' => __('Role Title'),
                'class' => 'required-entry',
            )
        );  
               
        $fieldset->addField('role_id', 'hidden', 
            array(
                'name'  => 'role_id',
                'id'    => 'role_id',
            )
        );    
        
        $roles = Mage::getResourceModel('permissions/roles_collection')->load()->toOptionArray();
        $roles[] = array('value' => 0, 'label' => '--- ROOT ---');
        
        $fieldset->addField('parent_id', 'select', 
            array(
                'name'  => 'parent_id',
                'label' => __('Role Parent'),
                'id'    => 'parent_id',
                'title' => __('Role Parent'),
                'class' => 'required-entry',
                'values'=> $roles,
            )
        );         

        $form->setValues($this->getRole()->getData());
        $this->setForm($form);
    }
}
