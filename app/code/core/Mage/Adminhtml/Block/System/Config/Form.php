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
    
    protected function _beforeToHtml()
    {
        $form = new Varien_Data_Form();
        $fieldset = $form->addFieldset('config_fieldset', array('legend'=>__('configuration form')));
        
        /**
         * @see  Varien_Object::__call()
         */
        $section = $this->getSection();
        
        if (!empty($section->fields)) {
            foreach ($section->fields->children() as $config) {
                $fieldset->addField((string) $config['name'], (string) $config['type'], array(
                    'label' => (string) $config['label'],
                    'value' => (string) Mage::getConfig()->getNode((string)$config['path']),
                    'class' => (string) $config['class']
                ));
            }
        }
        
        $this->setForm($form);
        return parent::_beforeToHtml();
    }
}
