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
    const SCOPE_DEFAULT = 'default';
    const SCOPE_WEBSITE = 'website';
    const SCOPE_STORE   = 'store';
    
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
        
        $defaultFieldsetRenderer = Mage::getHelper('adminhtml/system_config_form_fieldset');
        $defaultFieldRenderer = Mage::getHelper('adminhtml/system_config_form_field');
        $fieldset = array();
        
        foreach ($configFields->getItems() as $e) {
            if (!$this->_canShowField($e)) {
                continue;
            }
            $path = $e->getPath();
            $pathArr = explode('/', $path);
            $id = join('_', $pathArr);
            
            switch (sizeof($pathArr)) {
                case 1: // section
                    break;
                    
                case 2: // group
                	if ($e->getFrontendModel()) {
                		$fieldsetRenderer = Mage::getHelper($e->getFrontendModel());
                	} else {
                		$fieldsetRenderer = $defaultFieldsetRenderer;
                	}
                	
                	$fieldsetRenderer->setForm($this);
                	$fieldsetRenderer->setConfigData($configData);
                	
                    $fieldset[$pathArr[1]] = $form->addFieldset($pathArr[1], array(
                        'legend'=>__($e->getFrontendLabel())
                    ))->setRenderer($fieldsetRenderer);
                    $this->_addElementTypes($fieldset[$pathArr[1]]);
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
                	if ($e->getFrontendModel()) {
                		$fieldRenderer = Mage::getHelper($e->getFrontendModel());
                	} else {
                		$fieldRenderer = $defaultFieldRenderer;
                	}
                    $fieldType = $e->getFrontendType();
                    
                    $field = $fieldset[$pathArr[1]]->addField(
                        $id, $fieldType ? $fieldType : 'text', 
                        array(
                            'name'          => 'groups['.$pathArr[1].'][fields]['.$pathArr[2].'][value]',
                            'label'         => __($e->getFrontendLabel()),
                            'value'         => isset($data['value']) ? $data['value'] : '',
                            'default_value' => isset($data['default_value']) ? $data['default_value'] : '',
                            'old_value'     => isset($data['old_value']) ? $data['old_value'] : '',
                            'inherit'       => isset($data['inherit']) ? $data['inherit'] : '',
                            'class'         => $e->getFrontendClass(),
                            'can_use_default_value' => $this->canUseDefaultValue($e),
                            'can_use_website_value' => $this->canUseWebsiteValue($e),
                        ))->setRenderer($fieldRenderer);
                    if ($srcModel = $e->getSourceModel()) {
                        $field->setValues(Mage::getSingleton($srcModel)->toOptionArray());
                    }
                    break;
            }
        }
        
        $this->setForm($form);
        return $this;
    }
    
    public function canUseDefaultValue($field)
    {
        if ($this->getScope() == self::SCOPE_STORE && $field->getShowInDefault()) {
            return true;
        }
        if ($this->getScope() == self::SCOPE_WEBSITE && $field->getShowInDefault()) {
            return true;
        }
        return false;
    }

    public function canUseWebsiteValue($field)
    {
        if ($this->getScope() == self::SCOPE_STORE && $field->getShowInWebsite()) {
            return true;
        }
        return false;
    }
    
    /**
     * Checking field visibility
     *
     * @param   Varien_Object $field
     * @return  bool
     */
    protected function _canShowField($field)
    {
        switch ($this->getScope()) {
            case self::SCOPE_DEFAULT:
                return $field->getShowInDefault();
                break;
            case self::SCOPE_WEBSITE:
                return $field->getShowInWebsite();
                break;
            case self::SCOPE_STORE:
                return $field->getShowInStore();
                break;
        }
        return true;
    }
    
    /**
     * Retrieve current scope
     *
     * @return string
     */
    public function getScope()
    {
        $scope = $this->getData('scope');
        if (is_null($scope)) {
            $sectionCode = $this->getRequest()->getParam('section');
            $websiteCode = $this->getRequest()->getParam('website');
            $storeCode = $this->getRequest()->getParam('store');
            
            if (!$websiteCode && !$storeCode) {
                $scope = self::SCOPE_DEFAULT;
            }            
            elseif ($storeCode) {
                $scope = self::SCOPE_STORE;
            }
            elseif ($websiteCode) {
                $scope = self::SCOPE_WEBSITE;
            }
            else {
                $scope = false;
            }
            $this->setData('scope', $scope);
        }
        
        return $scope;
    }

    protected function _getAdditionalElementTypes()
    {
        return array(
            'export' => Mage::getConfig()->getBlockClassName('adminhtml/system_config_form_field_export'),
            'import' => Mage::getConfig()->getBlockClassName('adminhtml/system_config_form_field_import'),
        );
    }
}
