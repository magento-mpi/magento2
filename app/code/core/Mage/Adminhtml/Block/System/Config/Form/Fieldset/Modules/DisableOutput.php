<?php

class Mage_Adminhtml_Block_System_Config_Form_Fieldset_Modules_DisableOutput
	extends Mage_Adminhtml_Block_System_Config_Form_Fieldset
{
	protected $_dummyElement;
	protected $_fieldRenderer;
	protected $_values;
	
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
		$html = $this->_getHeaderHtml($element);
		
		$modules = array_keys((array)Mage::getConfig()->getNode('modules')->children());
		
		sort($modules);
		
        foreach ($modules as $moduleName) {
        	if ($moduleName==='Mage_Adminhtml') {
        		continue;
        	}
        	$html.= $this->_getFieldHtml($element, $moduleName);
        }
        $html .= $this->_getFooterHtml($element);

        return $html;
    }
    
    protected function _getDummyElement()
    {
    	if (empty($this->_dummyElement)) {
    		$this->_dummyElement = new Varien_Object(array('show_in_default'=>1, 'show_in_website'=>1));
    	}
    	return $this->_dummyElement;
    }    
    
    protected function _getFieldRenderer()
    {
    	if (empty($this->_fieldRenderer)) {
    		$this->_fieldRenderer = Mage::getHelper('adminhtml/system_config_form_field');
    	}
    	return $this->_fieldRenderer;
    }
    
    protected function _getValues()
    {
    	if (empty($this->_values)) {
    		$this->_values = array(
    			array('label'=>'Enable', 'value'=>0),
    			array('label'=>'Disable', 'value'=>1),
    		);
    	}
    	return $this->_values;
    }
    
    protected function _getFieldHtml($fieldset, $moduleName)
    {
    	$configData = $this->getConfigData();
    	$path = 'advanced/modules_disable_output/'.$moduleName; //TODO: move as property of form
    	$data = isset($configData[$path]) ? $configData[$path] : array();
    	
    	$e = $this->_getDummyElement();

        $field = $fieldset->addField($moduleName, 'select', 
            array(
                'name'          => 'groups[modules_disable_output][fields]['.$moduleName.'][value]',
                'label'         => $moduleName,
                'value'         => isset($data['value']) ? $data['value'] : '',
                'values'		=> $this->_getValues(),
                'default_value' => isset($data['default_value']) ? $data['default_value'] : '',
                'old_value'     => isset($data['old_value']) ? $data['old_value'] : '',
                'inherit'       => isset($data['inherit']) ? $data['inherit'] : '',
                'can_use_default_value' => $this->getForm()->canUseDefaultValue($e),
                'can_use_website_value' => $this->getForm()->canUseWebsiteValue($e),
            ))->setRenderer($this->_getFieldRenderer());
            
		return $field->toHtml();
    }
}