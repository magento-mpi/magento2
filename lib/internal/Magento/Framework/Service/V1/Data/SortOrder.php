<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Framework\Service\V1\Data;

use Magento\Framework\Service\Data\AbstractObject;

/**
 * Data object for sort order.
 */
class SortOrder extends AbstractObject
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
