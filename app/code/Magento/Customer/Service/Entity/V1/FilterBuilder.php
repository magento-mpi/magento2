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

class FilterBuilder extends \Magento\Service\Entity\AbstractDtoBuilder
{
    /**
     * Initializes the builder.
     */
    public function __construct()
    {
        parent::__construct();

        /* XXX: special constructor to set default values */
        $this->_data['condition_type'] = 'and';
    }

    /**
     * @param string $field
     * @return FilterBuilder
     */
    public function setField($field)
    {
        $this->_data['field'] = $field;
        return $this;
    }

    /**
     * @param string $value
     * @return FilterBuilder
     */
    public function setValue($value)
    {
        $this->_data['value'] = $value;
        return $this;
    }

    /**
     * @param string $conditionType
     * @return FilterBuilder
     */
    public function setConditionType($conditionType)
    {
        $this->_data['condition_type'] = $conditionType;
        return $this;
    }
}
