<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Service\V1\Data;

use Magento\Service\Data\AbstractObjectBuilder;

/**
 * Builder for Filter Data Object.
 *
 * @method Filter create()
 */
class FilterBuilder extends AbstractObjectBuilder
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
     * @param string | string[] $value
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
