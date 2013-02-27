<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Backend_Model_Config_Structure_Element_Dependency_Field
{
    /**
     * Values for dependence
     *
     * @var array
     */
    protected $_values;

    /**
     * Id of the dependent field
     *
     * @var string
     */
    protected $_dependentId;

    /**
     * Whether dependence is for negative comparison
     *
     * @var bool
     */
    protected $_isNegative = false;

    /**
     * @param array $data
     */
    public function __construct($data = array())
    {
        $fieldDataArray = $data['depends_field_data'];
        $fieldPrefix = $data['field_prefix'];
        if (isset($fieldDataArray['separator'])) {
            $this->_values = explode($fieldDataArray['separator'], $fieldDataArray['value']);
        } else {
            $this->_values = array($fieldDataArray['value']);
        }
        $fieldId = $fieldPrefix . array_pop($fieldDataArray['dependPath']);
        $fieldDataArray['dependPath'][] = $fieldId;
        $this->_dependentId = implode('_', $fieldDataArray['dependPath']);
        $this->_isNegative = isset($fieldDataArray['negative']) && $fieldDataArray['negative'];
    }

    /**
     * Check whether the value satisfy dependency
     *
     * @param string $value
     * @return bool
     */
    public function isValueSatisfy($value)
    {
        return !in_array($value, $this->_values) xor $this->_isNegative;
    }

    /**
     * Get id of the dependent field
     *
     * @return string
     */
    public function getDependentId()
    {
        return $this->_dependentId;
    }

    /**
     * Get values for dependence
     *
     * @return array
     */
    public function getValues()
    {
        return $this->_values;
    }

    /**
     * Get negative indication of dependency
     *
     * @return bool
     */
    public function isNegative()
    {
        return $this->_isNegative;
    }
}
