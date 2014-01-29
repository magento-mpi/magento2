<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Service\V1\Dto;

use Magento\Service\Entity\AbstractDtoBuilder;

/**
 * Builder for Filter DTO.
 */
class FilterBuilder extends AbstractDtoBuilder
{
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
