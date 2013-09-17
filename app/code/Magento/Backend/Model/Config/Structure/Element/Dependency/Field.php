<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Backend_Model_Config_Structure_Element_Dependency_Field
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
    protected $_id;

    /**
     * Whether dependence is for negative comparison
     *
     * @var bool
     */
    protected $_isNegative = false;

    /**
     * @param array $fieldData
     * @param string $fieldPrefix
     */
    public function __construct(array $fieldData = array(), $fieldPrefix = "")
    {
        if (isset($fieldData['separator'])) {
            $this->_values = explode($fieldData['separator'], $fieldData['value']);
        } else {
            $this->_values = array($fieldData['value']);
        }
        $fieldId = $fieldPrefix . (isset($fieldData['dependPath']) && is_array($fieldData['dependPath'])
            ? array_pop($fieldData['dependPath']) : '');
        $fieldData['dependPath'][] = $fieldId;
        $this->_id = implode('_', $fieldData['dependPath']);
        $this->_isNegative = isset($fieldData['negative']) && $fieldData['negative'];
    }

    /**
     * Check whether the value satisfy dependency
     *
     * @param string $value
     * @return bool
     */
    public function isValueSatisfy($value)
    {
        return in_array($value, $this->_values) xor $this->_isNegative;
    }

    /**
     * Get id of the dependent field
     *
     * @return string
     */
    public function getId()
    {
        return $this->_id;
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
