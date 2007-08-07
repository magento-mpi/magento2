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
        $configData = Mage::getResourceModel('adminhtml/config')
            ->loadSectionData($sectionCode, $websiteCode, $storeCode);
            
        $configFields = Mage::getResourceModel('core/config_field_collection')
            ->loadRecursive($sectionCode, $websiteCode, $storeCode);

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
                    $fieldset[$pathArr[1]] = $form->addFieldset($pathArr[1], array(
                        'legend'=>__($e->getFrontendLabel())
                    ))->setRenderer($fieldsetRenderer);
                    break;
                    
                case 3: // field
                    if (!isset($fieldset[$pathArr[1]])) {
                        continue;
                    }
                    if (isset($configData[$path])) {
                        $data = $configData[$path];
                    } else {
                        $data = array('value'=>'', 'default_value'=>'', 'old_value'=>'', 'inherit'=>'');
                    }
                    
                    $fieldType = $e->getFrontendType();
                    
                    $field = $fieldset[$pathArr[1]]->addField($id, $fieldType ? $fieldType : 'text', array(
                        'name'          => 'groups['.$pathArr[1].'][fields]['.$pathArr[2].'][value]',
                        'label'         => __($e->getFrontendLabel()),
                        'value'         => isset($data['value']) ? $data['value'] : '',
                        'default_value' => isset($data['default_value']) ? $data['default_value'] : '',
                        'old_value'     => isset($data['old_value']) ? $data['old_value'] : '',
                        'inherit'       => isset($data['inherit']) ? $data['inherit'] : '',
                        'class'         => $e->getFrontendClass(),
                    ))->setRenderer($fieldRenderer);
                    if ($e->getSourceModel()) {
                        $field->setValues(Mage::getModel($e->getSourceModel())->toOptionArray());
                    }
                    break;
            }
        }

        $this->setForm($form);
        return $this;
    }
}
