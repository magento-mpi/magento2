<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Framework\Api;

use Magento\Framework\Service\Data\AbstractExtensibleObjectBuilder;

/**
 * Builder for sort order data object.
 *
 * @method SortOrder create()
 */
class SortOrderBuilder extends AbstractExtensibleObjectBuilder
{
    /**
     * Set sorting field.
     *
     * @param string $field
     * @return $this
     */
    public function setField($field)
    {
        $this->_set(SortOrder::FIELD, $field);
        return $this;
    }

    /**
     * Set sorting direction.
     *
     * @param string $direction
     * @return $this
     */
    public function setDirection($direction)
    {
        $this->_set(SortOrder::DIRECTION, $direction);
        return $this;
    }
}
