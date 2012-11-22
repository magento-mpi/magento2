<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Backend_Model_Config_Structure_Element_Group
    extends Mage_Backend_Model_Config_Structure_Element_CompositeAbstract
{
    /**
     * Group clone model factory
     *
     * @var Mage_Backend_Model_Config_Clone_Factory
     */
    protected $_cloneModelFactory;

    /**
     * @param Mage_Backend_Model_Config_Structure_Element_Iterator $childrenIterator
     * @param Mage_Backend_Model_Config_Clone_Factory $cloneModelFactory
     */
    function __construct(
        Mage_Backend_Model_Config_Structure_Element_Iterator $childrenIterator,
        Mage_Backend_Model_Config_Clone_Factory $cloneModelFactory
    ) {
        $this->_cloneModelFactory = $cloneModelFactory;
        parent::__construct($childrenIterator);
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
     * @return Mage_Core_Model_Abstract
     */
    public function getCloneModel()
    {
        if (!isset($this->_data['clone_model']) || !$this->_data['clone_model']) {
            Mage::throwException('Config form fieldset clone model required to be able to clone fields');
        }
        return $this->_cloneModelFactory->create($this->_data['clone_model']);
    }

    /**
     * Populate form fieldset with group data
     *
     * @param Varien_Data_Form_Element_Fieldset $fieldset
     */
    public function populateFieldset(Varien_Data_Form_Element_Fieldset $fieldset)
    {
        $originalData = array();
        foreach ($this->_data as $key => $value) {
            if (!is_array($value)) {
                $originalData[$key] = $value;
            }
        }
        $fieldset->setOriginalData($originalData);
    }
}
