<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Data\CollectionBuilder;

use Magento\Framework\Api\ExtensibleObjectBuilder;

/**
 * Builder for Filter Service Data Object.
 *
 * @method Filter create()
 */
class FilterBuilder extends ExtensibleObjectBuilder
{
    /**
     * Set field
     *
     * @param string $field
     * @return $this
     */
    public function setField($field)
    {
        $this->data['field'] = $field;
        return $this;
    }

    /**
     * Set value
     *
     * @param string $value
     * @return $this
     */
    public function setValue($value)
    {
        $this->data['value'] = $value;
        return $this;
    }

    /**
     * Set condition type
     *
     * @param string $conditionType
     * @return $this
     */
    public function setConditionType($conditionType)
    {
        $this->data['condition_type'] = $conditionType;
        return $this;
    }
}
