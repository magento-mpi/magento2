<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Backend_Model_Config_Structure_Element_Group
    extends Magento_Backend_Model_Config_Structure_Element_CompositeAbstract
{
    /**
     * Group clone model factory
     *
     * @var Magento_Backend_Model_Config_Clone_Factory
     */
    protected $_cloneModelFactory;

    /**
     *
     * @var Magento_Backend_Model_Config_Structure_Element_Dependency_Mapper
     */
    protected $_dependencyMapper;

    /**
     * @param Magento_Core_Model_App $application
     * @param Magento_Backend_Model_Config_Structure_Element_Iterator_Field $childrenIterator
     * @param Magento_Backend_Model_Config_Clone_Factory $cloneModelFactory
     * @param Magento_Backend_Model_Config_Structure_Element_Dependency_Mapper $dependencyMapper
     */
    public function __construct(
        Magento_Core_Model_App $application,
        Magento_Backend_Model_Config_Structure_Element_Iterator_Field $childrenIterator,
        Magento_Backend_Model_Config_Clone_Factory $cloneModelFactory,
        Magento_Backend_Model_Config_Structure_Element_Dependency_Mapper $dependencyMapper
    ) {
        parent::__construct($application, $childrenIterator);
        $this->_cloneModelFactory = $cloneModelFactory;
        $this->_dependencyMapper = $dependencyMapper;
    }

    /**
     * Should group fields be cloned
     *
     * @return bool
     */
    public function shouldCloneFields()
    {
        return (isset($this->_data['clone_fields']) && !empty($this->_data['clone_fields']));
    }

    /**
     * Retrieve clone model
     *
     * @return Magento_Core_Model_Abstract
     * @throws Magento_Core_Exception
     */
    public function getCloneModel()
    {
        if (!isset($this->_data['clone_model']) || !$this->_data['clone_model']) {
            throw new Magento_Core_Exception('Config form fieldset clone model required to be able to clone fields');
        }
        return $this->_cloneModelFactory->create($this->_data['clone_model']);
    }

    /**
     * Populate form fieldset with group data
     *
     * @param Magento_Data_Form_Element_Fieldset $fieldset
     */
    public function populateFieldset(Magento_Data_Form_Element_Fieldset $fieldset)
    {
        $originalData = array();
        foreach ($this->_data as $key => $value) {
            if (!is_array($value)) {
                $originalData[$key] = $value;
            }
        }
        $fieldset->setOriginalData($originalData);
    }

    /**
     * Check whether group should be expanded
     *
     * @return bool
     */
    public function isExpanded()
    {
        return (bool) (isset($this->_data['expanded']) ? (int) $this->_data['expanded'] : false);
    }

    /**
     * Retrieve group fieldset css
     *
     * @return string
     */
    public function getFieldsetCss()
    {
        return array_key_exists('fieldset_css', $this->_data) ? $this->_data['fieldset_css'] : '';
    }

    /**
     * Retrieve field dependencies
     *
     * @param $storeCode
     * @return array
     */
    public function getDependencies($storeCode)
    {
        $dependencies = array();
        if (false == isset($this->_data['depends']['fields'])) {
            return $dependencies;
        }

        $dependencies = $this->_dependencyMapper->getDependencies($this->_data['depends']['fields'], $storeCode);
        return $dependencies;
    }
}
