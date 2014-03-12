<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Service\Data;

/**
 * Filter which can be used by any methods from service layer.
 */
class Filter extends \Magento\Service\Data\AbstractObject
{
    /**
     * Get field
     *
     * @return string
     */
    public function getField()
    {
        return $this->_get('field');
    }

    /**
     * Get value
     *
     * @return string
     */
    public function getValue()
    {
        return $this->_get('value');
    }

    /**
     * Get condition type
     *
     * @return string
     */
    public function getConditionType()
    {
        return $this->_get('condition_type');
    }
}
