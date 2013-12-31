<?php
/**
 * Customer Service Address Interface
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Service\Entity\V1;


class Filter extends \Magento\Service\Entity\AbstractDto
{
    /**
     * @param string $field
     * @param string $value
     * @param string $conditionType
     */
    public function __construct($field, $value, $conditionType = 'and')
    {
        parent::__construct();
        $this->_set('field', $field);
        $this->_set('value', $value);
        $this->_set('condition_type', $conditionType);
    }

    /**
     * @return string
     */
    public function getField()
    {
        return $this->_get('field');
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->_get('value');
    }

    /**
     * @return string
     */
    public function getConditionType()
    {
        return $this->_get('condition_type');
    }

    /**
     * @param string $field
     *
     * @return Filter
     */
    public function setField($field)
    {
        return $this->_set('field', $field);
    }

    /**
     * @param string $value
     *
     * @return Filter
     */
    public function setValue($value)
    {
        return $this->_set('value', $value);
    }
    /**
     * @param string $conditionType
     *
     * @return Filter
     */
    public function setConditionType($conditionType)
    {
        return $this->_set('condition_type', $conditionType);
    }


}