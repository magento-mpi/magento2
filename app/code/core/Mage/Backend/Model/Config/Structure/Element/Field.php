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
     * Backend model factory
     *
     * @var Mage_Backend_Model_Config_Backend_Factory
     */
    protected $_backendFactory;

    /**
     * @param Mage_Core_Model_Factory_Helper $helperFactory
     * @param Mage_Core_Model_Authorization $authorization
     * @param Mage_Backend_Model_Config_Backend_Factory $backendFactory
     */
    public function __construct(
        Mage_Core_Model_Factory_Helper $helperFactory,
        Mage_Core_Model_Authorization $authorization,
        Mage_Backend_Model_Config_Backend_Factory $backendFactory
    ) {
        parent::__construct($helperFactory, $authorization);
        $this->_backendFactory = $backendFactory;
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
     *
     * @return string
     */
    public function getComment($currentValue = '')
    {
        $comment = '';
        if (isset($this->_data['comment'])) {
            if (is_array($this->_data['comment'])) {
                if (isset($this->_data['comment']['model'])) {
                    $model = Mage::getModel($this->_data['comment']['model']);
                    if (method_exists($model, 'getCommentText')) {
                        $comment = $model->getCommentText($this->_data, $currentValue);
                    }
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
            return $this->getLayout()->createBlock($element['tooltip_block'])->toHtml();
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
     * Retrieve frontend css class
     *
     * @return string
     */
    public function getFrontendClass()
    {
        return isset($element['frontend_class']) ? $element['frontend_class'] : '';
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
     * Retrieve element config path
     *
     * @return string
     */
    public function getPath($fieldPrefix = '')
    {
        $path = isset($this->_data['path']) ? $this->_data['path'] : rand(0, 100000000);
        return $path . '/' . $fieldPrefix . $this->getId();
    }

    /**
     * Retrieve field section id
     *
     * @return string
     */
    public function getSectionId()
    {
        return basename($this->getPath());
    }

    /**
     * Retrieve field group path
     *
     * @return string
     */
    public function getGroupPath()
    {
        return dirname($this->getPath());
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
        if (isset($this->_data['validate'])) {
            $formField->addClass($this->_data['validate']);
        }

        if ($this->getType() == 'multiselect' && isset($this->_data['can_be_empty'])) {
            $formField->setCanBeEmpty(true);
        }

        if (isset($this->_data['source_model'])) {
            $formField->setValues($this->getOptions());
        }
    }

    /**
     * Retrieve source model option list
     *
     * @param array $element
     * @param string $path
     * @param string $fieldType
     * @return array
     */
    protected function getOptions()
    {
        $factoryName = $this->_data['source_model'];
        $method = false;
        if (preg_match('/^([^:]+?)::([^:]+?)$/', $factoryName, $matches)) {
            array_shift($matches);
            list($factoryName, $method) = array_values($matches);
        }

        $sourceModel = Mage::getSingleton($factoryName);
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
     * @param string $fieldPrefix
     * @return array
     */
    public function getDependencies($fieldPrefix)
    {
        $dependencies = array();
        foreach ($this->_data['depends']['fields'] as $depend) {
            /* @var array $depend */
            $dependentId = $section['id'] . '_' . $group['id'] . '_' . $fieldPrefix . $depend['id'];
            $shouldBeAddedDependence = true;
            $dependentValue = $depend['value'];
            if (isset($depend['separator'])) {
                $dependentValue = explode($depend['separator'], $dependentValue);
            }
            $dependentFieldName = $fieldPrefix . $depend['id'];
            $dependentField = $group['fields'][$dependentFieldName];
            /*
            * If dependent field can't be shown in current scope and real dependent config value
            * is not equal to preferred one, then hide dependence fields by adding dependence
            * based on not shown field (not rendered field)
            */
            if (!$this->_canShowField($dependentField)) {
                $dependentFullPath = $section['id'] . '/' . $group['id'] . '/' . $fieldPrefix . $depend['id'];
                $dependentValueInStore = Mage::getStoreConfig($dependentFullPath, $this->getStoreCode());
                if (is_array($dependentValue)) {
                    $shouldBeAddedDependence = !in_array($dependentValueInStore, $dependentValue);
                } else {
                    $shouldBeAddedDependence = $dependentValue != $dependentValueInStore;
                }
            }
            if ($shouldBeAddedDependence) {
                $dependencies[$dependentId] = $dependentValue;
            }
        }
        return $dependencies;
    }
}
