<?php
/**
 * System config form block
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Adminhtml_Block_System_Config_Form extends Mage_Adminhtml_Block_Widget_Form
{
    public function __construct() 
    {
        parent::__construct();
    }
    
    public function initForm()
    {
        /**
         * @see  Varien_Object::__call()
         */
        $section = $this->getSection();
        
        $form = new Varien_Data_Form();
        
        foreach ($section->groups->children() as $fieldsetName=>$fieldsetConfig) {
            $fieldset = $form->addFieldset($fieldsetName, array('legend'=>__((string)$fieldsetConfig->label)));
            $fieldset->setRenderer(
                $this->getLayout()->createBlock('adminhtml/system_config_form_element_renderer_fieldset')
            );
            
            if (empty($fieldsetConfig->fields)) {
                continue;
            }
            
            $field = $fieldset->addField('test', 'text',array(
                'label' => 'Test field',
            ));
            $field->setRenderer(
                $this->getLayout()->createBlock('adminhtml/system_config_form_element_renderer_input')
            );
            
            /*
            foreach ($fieldsetConfig->fields->children() as $fieldName=>$fieldConfig) {
                $frontend = $fieldConfig->frontend;
                
                $fieldType = isset($frontend->type) ? (string) $frontend->type : 'text';
                $fieldset->addField($fieldName, $fieldType, array(
                    'label' => (string) $frontend->label,
                    #'value' => (string) Mage::getConfig()->getNode((string) $fieldConfig['path']),
                    'class' => (string) $frontend->class,
                ));
            }
            */
        }

        $this->setForm($form);
        return $this;
    }
}
