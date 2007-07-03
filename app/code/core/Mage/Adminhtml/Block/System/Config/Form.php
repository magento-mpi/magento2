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
        /**
         * @see  Varien_Object::__call()
         */
        $section = $this->getSection();
        
        $form = new Varien_Data_Form();
        if (!empty($section->fieldset)) {
            foreach ($section->fieldset as $fieldsetConfig) {
                if (!empty($fieldsetConfig['name'])) {
                    $fieldsetName = (string)$fieldsetConfig['name'];
                } else {
                    $fieldsetName = $section->getName();
                }
                $legend = (string) $fieldsetConfig['legend'];
                $fieldset = $form->addFieldset($fieldsetName, array('legend'=>__($legend)));
                
                foreach ($fieldsetConfig->field as $fieldConfig) {
                    $fieldset->addField((string) $fieldConfig['name'], (string) $fieldConfig['type'], array(
                        'label' => (string) $fieldConfig['label'],
                        'value' => (string) Mage::getConfig()->getNode((string) $fieldConfig['path']),
                        'class' => (string) $fieldConfig['class']
                    ));
                }
            }
        }
        $this->setForm($form);
        return parent::_beforeToHtml();
    }
}
