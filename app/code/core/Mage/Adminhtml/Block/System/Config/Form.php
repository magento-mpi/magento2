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
        // get section fields from config xml
        
        $sectionCode = $this->getRequest()->getParam('section');
        $websiteCode = $this->getRequest()->getParam('website');
        $storeCode = $this->getRequest()->getParam('store');
        
        $isDefault = !$websiteCode && !$storeCode;
        
        // get config section data from database
        $configData = Mage::getResourceModel('core/config')
            ->loadWithDefaults($sectionCode, $websiteCode, $storeCode);
            
        $configFields = Mage::getResourceModel('core/config_field_collection')
            ->loadRecursive($sectionCode);

        $form = new Varien_Data_Form();
        
        $fieldsetRenderer = $this->getLayout()->createBlock('adminhtml/system_config_form_fieldset');
        $fieldRenderer = $this->getLayout()->createBlock('adminhtml/system_config_form_field');
        $fieldset = array();
        
        foreach ($configFields->getItems() as $e) {
            $path = $e->getPath();
            $pathArr = explode('/', $path);
            $id = join('_', $pathArr);
            
            switch (sizeof($pathArr)) {
                case 1: // section
                    break;
                    
                case 2: // group
                    $fieldset[$id] = $form->addFieldset($id, array(
                        'legend'=>__($e->getFrontendLabel())
                    ));
                    if (!$isDefault) {
                        $fieldset[$id]->setRenderer($fieldsetRenderer);
                    }
                    break;
                    
                case 3: // field
                    $fieldsetId = $pathArr[0].'_'.$pathArr[1];
                    
                    if (isset($configData[$path])) {
                        $data = $configData[$path];
                    } else {
                        $data = array('value'=>'', 'default_value'=>'', 'inherit'=>'');
                    }
                    
                    $fieldType = $e->getFrontendType();
                    
                    $field = $fieldset[$fieldsetId]->addField($id, $fieldType ? $fieldType : 'text', array(
                        'name' => $fieldsetId.'[fields]['.$pathArr[2].'][value]',
                        'label' => __($e->getFrontendLabel()),
                        'value' => $data['value'],
                        'defult_value' => $data['default_value'],
                        'inherit' => $data['inherit'],
                        'class' => $e->getFrontendClass(),
                    ));
                    if ($e->getSourceModel()) {
                        $field->setValues(Mage::getModel($e->getSourceModel())->toOptionArray());
                    }
                    if (!$isDefault) {
                        $field->setRenderer($fieldRenderer);
                    }
                    break;
            }
        }

        $this->setForm($form);
        return $this;
    }
}
