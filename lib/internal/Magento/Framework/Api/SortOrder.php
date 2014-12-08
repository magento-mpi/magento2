<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Framework\Api;


/**
 * Data object for sort order.
 */
class SortOrder extends AbstractExtensibleObject
{
    const FIELD = 'field';
    const DIRECTION = 'direction';

    /**
     * Get sorting field.
     *
     * @return string
     */
    public function getField()
    {
        return $this->_get(SortOrder::FIELD);
    }

    /**
     * Get sorting direction.
     *
     * @return string
     */
    public function getDirection()
    {
        return $this->_get(SortOrder::DIRECTION);
    }
}
