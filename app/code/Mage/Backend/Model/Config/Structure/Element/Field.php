<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Backend_Model_Config_Structure_Element_Field
    extends Mage_Backend_Model_Config_Structure_ElementAbstract
{

    /**
     * Default 'value' field for service option
     */
    const DEFAULT_VALUE_FIELD = 'id';

    /**
     * Default 'label' field for service option
     */
    const DEFAULT_LABEL_FIELD = 'name';

    /**
     * Default value for useEmptyValueOption for service option
     */
    const DEFAULT_INCLUDE_EMPTY_VALUE_OPTION = false;

    /**
     * Backend model factory
     *
     * @var Mage_Backend_Model_Config_BackendFactory
     */
    protected $_backendFactory;

    /**
     * Source model factory
     *
     * @var Mage_Backend_Model_Config_SourceFactory
     */
    protected $_sourceFactory;

    /**
     * Comment model factory
     *
     * @var Mage_Backend_Model_Config_CommentFactory
     */
    protected $_commentFactory;

    /**
     *
     * @var Mage_Backend_Model_Config_Structure_Element_Dependency_Mapper
     */
    protected $_dependencyMapper;

    /**
     * Block factory
     *
     * @var Mage_Core_Model_BlockFactory
     */
    protected $_blockFactory;

    /**
     * dataservice graph
     *
     * @var Mage_Core_Model_DataService_Graph
     */
     protected $_dataServiceGraph;

    /**
     * @param Mage_Core_Model_Factory_Helper $helperFactory
     * @param Mage_Core_Model_App $application
     * @param Mage_Backend_Model_Config_BackendFactory $backendFactory
     * @param Mage_Backend_Model_Config_SourceFactory $sourceFactory
     * @param Mage_Backend_Model_Config_CommentFactory $commentFactory
     * @param Mage_Core_Model_BlockFactory $blockFactory
     * @param Mage_Core_Model_DataService_Graph $dataServiceGraph,
     * @param Mage_Backend_Model_Config_Structure_Element_Dependency_Mapper $dependencyMapper
     */
    public function __construct(
        Mage_Core_Model_Factory_Helper $helperFactory,
        Mage_Core_Model_App $application,
        Mage_Backend_Model_Config_BackendFactory $backendFactory,
        Mage_Backend_Model_Config_SourceFactory $sourceFactory,
        Mage_Backend_Model_Config_CommentFactory $commentFactory,
        Mage_Core_Model_BlockFactory $blockFactory,
        Mage_Core_Model_DataService_Graph $dataServiceGraph,
        Mage_Backend_Model_Config_Structure_Element_Dependency_Mapper $dependencyMapper
    ) {
        parent::__construct($helperFactory, $application);
        $this->_backendFactory = $backendFactory;
        $this->_sourceFactory = $sourceFactory;
        $this->_commentFactory = $commentFactory;
        $this->_blockFactory = $blockFactory;
        $this->_dataServiceGraph = $dataServiceGraph;
        $this->_dependencyMapper = $dependencyMapper;
    }

    /**
     * Retrieve field label
     *
     * @param string $labelPrefix
     * @return string
     */
    public function getLabel($labelPrefix = '')
    {
        $label = '';
        if ($labelPrefix) {
            $label .= $this->_helperFactory->get($this->_getTranslationModule())->__($labelPrefix) . ' ';
        }
        $label .= parent::getLabel();
        return $label;
    }

    /**
     * Retrieve field hint
     *
     * @return string
     */
    public function getHint()
    {
        return $this->_getTranslatedAttribute('hint');
    }

    /**
     * Retrieve comment
     *
     * @param string $currentValue
     * @return string
     */
    public function getComment($currentValue = '')
    {
        $comment = '';
        if (isset($this->_data['comment'])) {
            if (is_array($this->_data['comment'])) {
                if (isset($this->_data['comment']['model'])) {
                    $model = $this->_commentFactory->create($this->_data['comment']['model']);
                    $comment = $model->getCommentText($currentValue);
                }
            } else {
                $comment = parent::getComment();
            }
        }
        return $comment;
    }

    /**
     * Retrieve tooltip text
     *
     * @return string
     */
    public function getTooltip()
    {
        if (isset($this->_data['tooltip'])) {
            return $this->_getTranslatedAttribute('tooltip');
        } elseif (isset($this->_data['tooltip_block'])) {
            return $this->_blockFactory->createBlock($this->_data['tooltip_block'])->toHtml();
        }
        return '';
    }

    /**
     * Retrieve field type
     *
     * @return string
     */
    public function getType()
    {
        return isset($this->_data['type']) ? $this->_data['type'] : 'text';
    }

    /**
     * Get required elements paths for the field
     *
     * @param string $fieldPrefix
     * @param string $elementType
     * @return array
     */
    protected function _getRequiredElements($fieldPrefix = '', $elementType = 'group')
    {
        $elements = array();
        if (isset($this->_data['requires'][$elementType])) {
            if (isset($this->_data['requires'][$elementType]['id'])) {
                $elements[] = $this->_getPath($this->_data['requires'][$elementType]['id'], $fieldPrefix);
            } else {
                foreach ($this->_data['requires'][$elementType] as $element) {
                    $elements[] = $this->_getPath($element['id'], $fieldPrefix);
                }
            }
        }
        return $elements;
    }

    /**
     * Get required groups paths for the field
     *
     * @param string $fieldPrefix
     * @return array
     */
    public function getRequiredGroups($fieldPrefix = '')
    {
        return $this->_getRequiredElements($fieldPrefix, 'group');
    }


    /**
     * Get required fields paths for the field
     *
     * @param string $fieldPrefix
     * @return array
     */
    public function getRequiredFields($fieldPrefix = '')
    {
        return $this->_getRequiredElements($fieldPrefix, 'field');
    }

    /**
     * Retrieve frontend css class
     *
     * @return string
     */
    public function getFrontendClass()
    {
        return isset($this->_data['frontend_class']) ? $this->_data['frontend_class'] : '';
    }

    /**
     * Check whether field has backend model
     *
     * @return bool
     */
    public function hasBackendModel()
    {
        return array_key_exists('backend_model', $this->_data) && $this->_data['backend_model'];
    }

    /**
     * Retrieve backend model
     *
     * @return Mage_Core_Model_Config_Data
     */
    public function getBackendModel()
    {
        return $this->_backendFactory->create($this->_data['backend_model']);
    }

    /**
     * Retrieve field section id
     *
     * @return string
     */
    public function getSectionId()
    {
        $parts = explode('/', $this->getConfigPath() ?: $this->getPath());
        return current($parts);
    }

    /**
     * Retrieve field group path
     *
     * @return string
     */
    public function getGroupPath()
    {
        return dirname($this->getConfigPath() ?: $this->getPath());
    }

    /**
     * Retrieve config path
     *
     * @return null|string
     */
    public function getConfigPath()
    {
        return isset($this->_data['config_path']) ? $this->_data['config_path'] : null;
    }

    /**
     * Check whether field should be shown in default scope
     *
     * @return bool
     */
    public function showInDefault()
    {
        return isset($this->_data['showInDefault']) && (int)$this->_data['showInDefault'];
    }

    /**
     * Check whether field should be shown in website scope
     *
     * @return bool
     */
    public function showInWebsite()
    {
        return isset($this->_data['showInWebsite']) && (int)$this->_data['showInWebsite'];
    }

    /**
     * Check whether field should be shown in store scope
     *
     * @return bool
     */
    public function showInStore()
    {
        return isset($this->_data['showInStore']) && (int)$this->_data['showInStore'];
    }

    /**
     * Populate form element with field data
     *
     * @param Varien_Data_Form_Element_Abstract $formField
     */
    public function populateInput($formField)
    {
        $originalData = array();
        foreach ($this->_data as $key => $value) {
            if (!is_array($value)) {
                $originalData[$key] = $value;
            }
        }
        $formField->setOriginalData($originalData);
    }

    /**
     * Check whether field has validation class
     *
     * @return bool
     */
    public function hasValidation()
    {
        return isset($this->_data['validate']);
    }

    /**
     * Retrieve field validation class
     *
     * @return string
     */
    public function getValidation()
    {
        return isset($this->_data['validate']) ? $this->_data['validate'] : null;
    }

    /**
     * Check whether field can be empty
     *
     * @return bool
     */
    public function canBeEmpty()
    {
        return isset($this->_data['can_be_empty']);
    }

    /**
     * Check whether field has source model
     *
     * @return bool
     */
    public function hasSourceModel()
    {
        return isset($this->_data['source_model']);
    }

    /**
     * Check whether field has options or source model
     *
     * @return bool
     */
    public function hasOptions()
    {
        return isset($this->_data['source_model']) || isset($this->_data['options'])
            || isset($this->_data['source_service']);
    }

    /**
     * Retrieve static options, source service options or source model option list
     *
     * @return array
     */
    public function getOptions()
    {
        if (isset($this->_data['source_model'])) {
            $sourceModel = $this->_data['source_model'];
            $optionArray = $this->_getOptionsFromSourceModel($sourceModel);
            return $optionArray;
        } else if (isset($this->_data['source_service'])) {
            $sourceService = $this->_data['source_service'];
            $options = $this->_getOptionsFromService($sourceService);
            return $options;
        } else if (isset($this->_data['options']) && isset($this->_data['options']['option'])) {
            $options = $this->_data['options']['option'];
            $options = $this->_getStaticOptions($options);
            return $options;
        }
        return array();
    }

    /**
     * Get Static Options list
     *
     * @param array $options
     * @return array
     */
    protected  function _getStaticOptions(array $options)
    {
        foreach (array_keys($options) as $key) {
            $options[$key]['label'] = $this->_translateLabel($options[$key]['label']);
            $options[$key]['value'] = $this->_fillInConstantPlaceholders($options[$key]['value']);
        }
        return $options;
    }

    /**
     * Retrieve the options list from the specified service call.
     *
     * @param $sourceService
     * @return array
     */
    protected function _getOptionsFromService($sourceService)
    {
        $valueField = self::DEFAULT_VALUE_FIELD;
        $labelField = self::DEFAULT_LABEL_FIELD;
        $inclEmptyValOption = self::DEFAULT_INCLUDE_EMPTY_VALUE_OPTION;
        $serviceCall = $sourceService['service_call'];
        if (isset($sourceService['idField'])) {
            $valueField = $sourceService['idField'];
        }
        if (isset($sourceService['labelField'])) {
            $labelField = $sourceService['labelField'];
        }
        if (isset($sourceService['includeEmptyValueOption'])) {
            $inclEmptyValOption = $sourceService['includeEmptyValueOption'];
        }
        $dataCollection = $this->_dataServiceGraph->get($serviceCall);
        $options = array();
        if ($inclEmptyValOption) {
            $options[] = array('value' => '', 'label' => '-- Please Select --');
        }
        foreach ($dataCollection as $dataItem) {
            $options[] = array(
                'value' => $dataItem[$valueField],
                'label' => $this->_translateLabel($dataItem[$labelField])
            );
        }
        return $options;
    }

    /**
     * @param $label an option label that should be translated
     * @return string the translated version of the input label
     */
    private function _translateLabel($label)
    {
        return $this->_helperFactory->get(
            $this->_getTranslationModule())->__($label);
    }

    /**
     * @param $value an option value that may contain a placeholder for a constant value
     * @return mixed|string the value after being replaced by the constant if needed
     */
    private function _fillInConstantPlaceholders($value)
    {
        if (is_string($value) && preg_match('/^{{([A-Z][A-Za-z\d_]+::[A-Z\d_]+)}}$/', $value, $matches)) {
            $value = constant($matches[1]);
        }
        return $value;
    }

    /**
     * Retrieve options list from source model
     *
     * @param $sourceModel
     * @return array
     */
    protected function _getOptionsFromSourceModel($sourceModel)
    {
        $method = false;
        if (preg_match('/^([^:]+?)::([^:]+?)$/', $sourceModel, $matches)) {
            array_shift($matches);
            list($sourceModel, $method) = array_values($matches);
        }

        $sourceModel = $this->_sourceFactory->create($sourceModel);
        if ($sourceModel instanceof Varien_Object) {
            $sourceModel->setPath($this->getPath());
        }
        if ($method) {
            if ($this->getType() == 'multiselect') {
                $optionArray = $sourceModel->$method();
            } else {
                $optionArray = array();
                foreach ($sourceModel->$method() as $key => $value) {
                    if (is_array($value)) {
                        $optionArray[] = $value;
                    } else {
                        $optionArray[] = array('label' => $value, 'value' => $key);
                    }
                }
            }
        } else {
            $optionArray = $sourceModel->toOptionArray($this->getType() == 'multiselect');
        }
        return $optionArray;
    }

    /**
     * Retrieve field dependencies
     *
     * @param $fieldPrefix
     * @param $storeCode
     * @return array
     */
    public function getDependencies($fieldPrefix, $storeCode)
    {
        $dependencies = array();
        if (false == isset($this->_data['depends']['fields'])) {
            return $dependencies;
        }
        $dependencies = $this->_dependencyMapper->getDependencies(
            $this->_data['depends']['fields'],
            $storeCode,
            $fieldPrefix
        );
        return $dependencies;
    }

    /**
     * Check whether element should be displayed for advanced users
     *
     * @return bool
     */
    public function isAdvanced()
    {
        return isset($this->_data['advanced']) && $this->_data['advanced'];
    }
}
