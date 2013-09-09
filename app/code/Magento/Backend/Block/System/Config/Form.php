<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * System config form block
 *
 * @category   Magento
 * @package    Magento_Backend
 * @author      Magento Core Team <core@magentocommerce.com>
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Magento_Backend_Block_System_Config_Form extends Magento_Backend_Block_Widget_Form
{

    const SCOPE_DEFAULT = 'default';
    const SCOPE_WEBSITES = 'websites';
    const SCOPE_STORES   = 'stores';

    /**
     * Config data array
     *
     * @var array
     */
    protected $_configData;

    /**
     * Backend config data instance
     *
     * @var Magento_Backend_Model_Config
     */
    protected $_configDataObject;

    /**
     * Default fieldset rendering block
     *
     * @var Magento_Backend_Block_System_Config_Form_Fieldset
     */
    protected $_fieldsetRenderer;

    /**
     * Default field rendering block
     *
     * @var Magento_Backend_Block_System_Config_Form_Field
     */
    protected $_fieldRenderer;

    /**
     * List of fieldset
     *
     * @var array
     */
    protected $_fieldsets = array();

    /**
     * Translated scope labels
     *
     * @var array
     */
    protected $_scopeLabels = array();

    /**
     * Backend Config model factory
     *
     * @var Magento_Backend_Model_Config_Factory
     */
    protected $_configFactory;

    /**
     * Magento_Data_Form_Factory
     *
     * @var Magento_Data_Form_Factory
     */
    protected $_formFactory;

    /**
     * System config structure
     *
     * @var Magento_Backend_Model_Config_Structure
     */
    protected $_configStructure;

    /**
     *Form fieldset factory
     *
     * @var Magento_Backend_Block_System_Config_Form_Fieldset_Factory
     */
    protected $_fieldsetFactory;

    /**
     * Form field factory
     *
     * @var Magento_Backend_Block_System_Config_Form_Field_Factory
     */
    protected $_fieldFactory;

    /**
     * Form field factory
     *
     * @var Magento_Core_Model_Config
     */
    protected $_config;

    /**
     * @param Magento_Backend_Block_Template_Context $context
     * @param Magento_Backend_Model_Config_Factory $configFactory
     * @param Magento_Data_Form_Factory $formFactory
     * @param Magento_Backend_Model_Config_Clone_Factory $cloneModelFactory
     * @param Magento_Backend_Model_Config_Structure $configStructure
     * @param Magento_Backend_Block_System_Config_Form_Fieldset_Factory $fieldsetFactory
     * @param Magento_Backend_Block_System_Config_Form_Field_Factory $fieldFactory
     * @param Magento_Core_Model_Config $coreConfig
     * @param array $data
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        Magento_Backend_Block_Template_Context $context,
        Magento_Backend_Model_Config_Factory $configFactory,
        Magento_Data_Form_Factory $formFactory,
        Magento_Backend_Model_Config_Clone_Factory $cloneModelFactory,
        Magento_Backend_Model_Config_Structure $configStructure,
        Magento_Backend_Block_System_Config_Form_Fieldset_Factory $fieldsetFactory,
        Magento_Backend_Block_System_Config_Form_Field_Factory $fieldFactory,
        Magento_Core_Model_Config $coreConfig,
        array $data = array()
    ) {
        parent::__construct($context, $data);
        $this->_configFactory = $configFactory;
        $this->_formFactory = $formFactory;
        $this->_cloneModelFactory = $cloneModelFactory;
        $this->_configStructure = $configStructure;
        $this->_fieldsetFactory = $fieldsetFactory;
        $this->_fieldFactory = $fieldFactory;
        $this->_config = $coreConfig;

        $this->_scopeLabels = array(
            self::SCOPE_DEFAULT  => __('[GLOBAL]'),
            self::SCOPE_WEBSITES => __('[WEBSITE]'),
            self::SCOPE_STORES   => __('[STORE VIEW]'),
        );
    }

    /**
     * Initialize objects required to render config form
     *
     * @return Magento_Backend_Block_System_Config_Form
     */
    protected function _initObjects()
    {
        $this->_configDataObject = $this->_configFactory->create(array(
            'data' => array(
                'section' => $this->getSectionCode(),
                'website' => $this->getWebsiteCode(),
                'store' => $this->getStoreCode()
            )
        ));

        $this->_configData = $this->_configDataObject->load();
        $this->_fieldsetRenderer = $this->_fieldsetFactory->create();
        $this->_fieldRenderer = $this->_fieldFactory->create();
        return $this;
    }

    /**
     * Initialize form
     *
     * @return Magento_Backend_Block_System_Config_Form
     */
    public function initForm()
    {
        $this->_initObjects();

        /** @var Magento_Data_Form $form */
        $form = $this->_formFactory->create();
        /** @var $section Magento_Backend_Model_Config_Structure_Element_Section */
        $section = $this->_configStructure->getElement($this->getSectionCode());
        if ($section && $section->isVisible($this->getWebsiteCode(), $this->getStoreCode())) {
            foreach ($section->getChildren() as $group) {
                $this->_initGroup($group, $section, $form);
            }
        }

        $this->setForm($form);
        return $this;
    }

    /**
     * Initialize config field group
     *
     * @param Magento_Backend_Model_Config_Structure_Element_Group $group
     * @param Magento_Backend_Model_Config_Structure_Element_Section $section
     * @param Magento_Data_Form_Abstract $form
     */
    protected function _initGroup(
        Magento_Backend_Model_Config_Structure_Element_Group $group,
        Magento_Backend_Model_Config_Structure_Element_Section $section,
        Magento_Data_Form_Abstract $form
    ) {
        $frontendModelClass = $group->getFrontendModel();
        $fieldsetRenderer = $frontendModelClass ?
            Mage::getBlockSingleton($frontendModelClass) :
            $this->_fieldsetRenderer;

        $fieldsetRenderer->setForm($this);
        $fieldsetRenderer->setConfigData($this->_configData);
        $fieldsetRenderer->setGroup($group);

        $fieldsetConfig = array(
            'legend' => $group->getLabel(),
            'comment' => $group->getComment(),
            'expanded' => $group->isExpanded(),
            'group' => $group->getData()
        );

        $fieldset = $form->addFieldset($this->_generateElementId($group->getPath()), $fieldsetConfig);
        $fieldset->setRenderer($fieldsetRenderer);
        $group->populateFieldset($fieldset);
        $this->_addElementTypes($fieldset);

        $dependencies = $group->getDependencies($this->getStoreCode());
        $elementName = $this->_generateElementName($group->getPath());
        $elementId = $this->_generateElementId($group->getPath());

        $this->_populateDependenciesBlock($dependencies, $elementId, $elementName);

        if ($group->shouldCloneFields()) {
            $cloneModel = $group->getCloneModel();
            foreach ($cloneModel->getPrefixes() as $prefix) {
                $this->initFields($fieldset, $group, $section, $prefix['field'], $prefix['label']);
            }
        } else {
            $this->initFields($fieldset, $group, $section);
        }

        $this->_fieldsets[$group->getId()] = $fieldset;
    }

    /**
     * Return dependency block object
     *
     * @return Magento_Backend_Block_Widget_Form_Element_Dependence
     */
    protected function _getDependence()
    {
        if (!$this->getChildBlock('element_dependence')) {
            $this->addChild('element_dependence', 'Magento_Backend_Block_Widget_Form_Element_Dependence');
        }
        return $this->getChildBlock('element_dependence');
    }

    /**
     * Initialize config group fields
     *
     * @param Magento_Data_Form_Element_Fieldset $fieldset
     * @param Magento_Backend_Model_Config_Structure_Element_Group $group
     * @param Magento_Backend_Model_Config_Structure_Element_Section $section
     * @param string $fieldPrefix
     * @param string $labelPrefix
     * @return Magento_Backend_Block_System_Config_Form
     */
    public function initFields(
        Magento_Data_Form_Element_Fieldset $fieldset,
        Magento_Backend_Model_Config_Structure_Element_Group $group,
        Magento_Backend_Model_Config_Structure_Element_Section $section,
        $fieldPrefix = '',
        $labelPrefix = ''
    ) {
        if (!$this->_configDataObject) {
            $this->_initObjects();
        }

        // Extends for config data
        $extraConfigGroups = array();

        /** @var $element Magento_Backend_Model_Config_Structure_Element_Field */
        foreach ($group->getChildren() as $element) {
            if ($element instanceof Magento_Backend_Model_Config_Structure_Element_Group) {
                $this->_initGroup($element, $section, $fieldset);
            } else {
                $path = $element->getConfigPath() ?: $element->getPath($fieldPrefix);
                if ($element->getSectionId() != $section->getId()) {
                    $groupPath = $element->getGroupPath();
                    if (!isset($extraConfigGroups[$groupPath])) {
                        $this->_configData = $this->_configDataObject
                            ->extendConfig($groupPath, false, $this->_configData);
                        $extraConfigGroups[$groupPath] = true;
                    }
                }
                $this->_initElement($element, $fieldset, $path, $fieldPrefix, $labelPrefix);
            }
        }
        return $this;
    }

    /**
     * Initialize form element
     *
     * @param Magento_Backend_Model_Config_Structure_Element_Field $field
     * @param Magento_Data_Form_Element_Fieldset $fieldset
     * @param $path
     * @param string $fieldPrefix
     * @param string $labelPrefix
     */
    protected function _initElement(
        Magento_Backend_Model_Config_Structure_Element_Field $field,
        Magento_Data_Form_Element_Fieldset $fieldset,
        $path,
        $fieldPrefix = '',
        $labelPrefix = ''
    ) {
        $inherit = true;
        $data = null;
        if (array_key_exists($path, $this->_configData)) {
            $data = $this->_configData[$path];
            $inherit = false;
        } elseif ($field->getConfigPath() !== null) {
            $data = $this->getConfigValue($field->getConfigPath());
        } else {
            $data = $this->getConfigValue($path);
        }
        $fieldRendererClass = $field->getFrontendModel();
        if ($fieldRendererClass) {
            $fieldRenderer = Mage::getBlockSingleton($fieldRendererClass);
        } else {
            $fieldRenderer = $this->_fieldRenderer;
        }

        $fieldRenderer->setForm($this);
        $fieldRenderer->setConfigData($this->_configData);

        $elementName = $this->_generateElementName($field->getPath(), $fieldPrefix);
        $elementId = $this->_generateElementId($field->getPath($fieldPrefix));

        if ($field->hasBackendModel()) {
            $backendModel = $field->getBackendModel();
            $backendModel->setPath($path)
                ->setValue($data)
                ->setWebsite($this->getWebsiteCode())
                ->setStore($this->getStoreCode())
                ->afterLoad();
            $data = $backendModel->getValue();
        }

        $dependencies = $field->getDependencies($fieldPrefix, $this->getStoreCode());
        $this->_populateDependenciesBlock($dependencies, $elementId, $elementName);

        $sharedClass = $this->_getSharedCssClass($field);
        $requiresClass = $this->_getRequiresCssClass($field, $fieldPrefix);

        $formField = $fieldset->addField($elementId, $field->getType(), array(
            'name' => $elementName,
            'label' => $field->getLabel($labelPrefix),
            'comment' => $field->getComment($data),
            'tooltip' => $field->getTooltip(),
            'hint' => $field->getHint(),
            'value' => $data,
            'inherit' => $inherit,
            'class' => $field->getFrontendClass() . $sharedClass . $requiresClass,
            'field_config' => $field->getData(),
            'scope' => $this->getScope(),
            'scope_id' => $this->getScopeId(),
            'scope_label' => $this->getScopeLabel($field),
            'can_use_default_value' => $this->canUseDefaultValue($field->showInDefault()),
            'can_use_website_value' => $this->canUseWebsiteValue($field->showInWebsite()),
        ));
        $field->populateInput($formField);

        if ($field->hasValidation()) {
            $formField->addClass($field->getValidation());
        }
        if ($field->getType() == 'multiselect') {
            $formField->setCanBeEmpty($field->canBeEmpty());
        }
        if ($field->hasOptions()) {
            $formField->setValues($field->getOptions());
        }
        $formField->setRenderer($fieldRenderer);
    }

    /**
     * Populate dependencies block
     *
     * @param array $dependencies
     * @param string $elementId
     * @param string $elementName
     */
    protected function _populateDependenciesBlock(array $dependencies, $elementId, $elementName)
    {
        foreach ($dependencies as $dependentField) {
            /** @var $dependentField Magento_Backend_Model_Config_Structure_Element_Dependency_Field */
            $fieldNameFrom = $this->_generateElementName($dependentField->getId(), null, '_');
            $this->_getDependence()
                ->addFieldMap($elementId, $elementName)
                ->addFieldMap($this->_generateElementId($dependentField->getId()), $fieldNameFrom)
                ->addFieldDependence($elementName, $fieldNameFrom, $dependentField);
        }
    }

    /**
     * Generate element name
     *
     * @param string $elementPath
     * @param string $fieldPrefix
     * @param string $separator
     * @return string
     */
    protected function _generateElementName($elementPath, $fieldPrefix = '', $separator = '/')
    {
        $part = explode($separator, $elementPath);
        array_shift($part); //shift section name
        $fieldId = array_pop($part);   //shift filed id
        $groupName = implode('][groups][', $part);
        $name = 'groups[' . $groupName . '][fields][' . $fieldPrefix . $fieldId . '][value]';
        return $name;
    }

    /**
     * Generate element id
     *
     * @param string $path
     * @return string
     */
    protected function _generateElementId($path)
    {
        return str_replace('/', '_', $path);
    }

    /**
     * Get config value
     *
     * @param string $path
     * @return mixed
     */
    public function getConfigValue($path)
    {
        return $this->_config->getValue($path, $this->getScope(), $this->getScopeCode());
        if (empty($this->_configRoot)) {
            $this->_configRoot = $this->_coreConfig->getNode(null, $this->getScope(), $this->getScopeCode());
        }
        return $this->_configRoot;
    }

    /**
     *
     *
     * @return Magento_Backend_Block_Widget_Form|Magento_Core_Block_Abstract|void
     */
    protected function _beforeToHtml()
    {
        $this->initForm();
        return parent::_beforeToHtml();
    }

    /**
     * Append dependence block at then end of form block
     *
     * @param string $html
     * @return string
     */
    protected function _afterToHtml($html)
    {
        if ($this->_getDependence()) {
            $html .= $this->_getDependence()->toHtml();
        }
        $html = parent::_afterToHtml($html);
        return $html;
    }

    /**
     * Check if can use default value
     *
     * @param int $fieldValue
     * @return boolean
     */
    public function canUseDefaultValue($fieldValue)
    {
        if ($this->getScope() == self::SCOPE_STORES && $fieldValue) {
            return true;
        }
        if ($this->getScope() == self::SCOPE_WEBSITES && $fieldValue) {
            return true;
        }
        return false;
    }

    /**
     * Check if can use website value
     *
     * @param int $fieldValue
     * @return boolean
     */
    public function canUseWebsiteValue($fieldValue)
    {
        if ($this->getScope() == self::SCOPE_STORES && $fieldValue) {
            return true;
        }
        return false;
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
            if ($this->getStoreCode()) {
                $scope = self::SCOPE_STORES;
            } elseif ($this->getWebsiteCode()) {
                $scope = self::SCOPE_WEBSITES;
            } else {
                $scope = self::SCOPE_DEFAULT;
            }
            $this->setScope($scope);
        }

        return $scope;
    }

    /**
     * Retrieve label for scope
     *
     * @param Magento_Backend_Model_Config_Structure_Element_Field $field
     * @return string
     */
    public function getScopeLabel(Magento_Backend_Model_Config_Structure_Element_Field $field)
    {
        $showInStore = $field->showInStore();
        $showInWebsite = $field->showInWebsite();

        if ($showInStore == 1) {
            return $this->_scopeLabels[self::SCOPE_STORES];
        } elseif ($showInWebsite == 1) {
            return $this->_scopeLabels[self::SCOPE_WEBSITES];
        }
        return $this->_scopeLabels[self::SCOPE_DEFAULT];
    }

    /**
     * Get current scope code
     *
     * @return string
     */
    public function getScopeCode()
    {
        $scopeCode = $this->getData('scope_code');
        if (is_null($scopeCode)) {
            if ($this->getStoreCode()) {
                $scopeCode = $this->getStoreCode();
            } elseif ($this->getWebsiteCode()) {
                $scopeCode = $this->getWebsiteCode();
            } else {
                $scopeCode = '';
            }
            $this->setScopeCode($scopeCode);
        }

        return $scopeCode;
    }

    /**
     * Get current scope code
     *
     * @return int|string
     */
    public function getScopeId()
    {
        $scopeId = $this->getData('scope_id');
        if (is_null($scopeId)) {
            if ($this->getStoreCode()) {
                $scopeId = Mage::app()->getStore($this->getStoreCode())->getId();
            } elseif ($this->getWebsiteCode()) {
                $scopeId = Mage::app()->getWebsite($this->getWebsiteCode())->getId();
            } else {
                $scopeId = '';
            }
            $this->setScopeId($scopeId);
        }
        return $scopeId;
    }

    /**
     * Get additional element types
     *
     * @return array
     */
    protected function _getAdditionalElementTypes()
    {
        return array(
            'export' => 'Magento_Backend_Block_System_Config_Form_Field_Export',
            'import' => 'Magento_Backend_Block_System_Config_Form_Field_Import',
            'allowspecific' => 'Magento_Backend_Block_System_Config_Form_Field_Select_Allowspecific',
            'image' => 'Magento_Backend_Block_System_Config_Form_Field_Image',
            'file' => 'Magento_Backend_Block_System_Config_Form_Field_File',
        );
    }

    /**
     * Temporary moved those $this->getRequest()->getParam('blabla') from the code accross this block
     * to getBlala() methods to be later set from controller with setters
     */
    /**
     * Enter description here...
     *
     * @TODO delete this methods when {^see above^} is done
     * @return string
     */
    public function getSectionCode()
    {
        return $this->getRequest()->getParam('section', '');
    }

    /**
     * Enter description here...
     *
     * @TODO delete this methods when {^see above^} is done
     * @return string
     */
    public function getWebsiteCode()
    {
        return $this->getRequest()->getParam('website', '');
    }

    /**
     * Enter description here...
     *
     * @TODO delete this methods when {^see above^} is done
     * @return string
     */
    public function getStoreCode()
    {
        return $this->getRequest()->getParam('store', '');
    }

    /**
     * Get css class for "shared" functionality
     *
     * @param Magento_Backend_Model_Config_Structure_Element_Field $field
     * @return string
     */
    protected function _getSharedCssClass(Magento_Backend_Model_Config_Structure_Element_Field $field)
    {
        $sharedClass = '';
        if ($field->getAttribute('shared') && $field->getConfigPath()) {
            $sharedClass = ' shared shared-' . str_replace('/', '-', $field->getConfigPath());
            return $sharedClass;
        }
        return $sharedClass;
    }

    /**
     * Get css class for "requires" functionality
     *
     * @param Magento_Backend_Model_Config_Structure_Element_Field $field
     * @param $fieldPrefix
     * @return string
     */
    protected function _getRequiresCssClass(Magento_Backend_Model_Config_Structure_Element_Field $field, $fieldPrefix)
    {
        $requiresClass = '';
        $requiredPaths = array_merge($field->getRequiredFields($fieldPrefix), $field->getRequiredGroups($fieldPrefix));
        if (!empty($requiredPaths)) {
            $requiresClass = ' requires';
            foreach ($requiredPaths as $requiredPath) {
                $requiresClass .= ' requires-' . $this->_generateElementId($requiredPath);
            }
            return $requiresClass;
        }
        return $requiresClass;
    }
}
