<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tax\Api\Data;

/**
 * @see \Magento\Tax\Service\V1\Data\ZipRange
 */
interface ZipRangeInterface
{
    /**#@+
     * Constants defined for keys of array, makes typos less likely
     */
    const KEY_FROM = 'from';

    const KEY_TO = 'to';

    /**#@-*/

    /**
     * Get zip range starting point
     *
     * @return int
     */
    public function getFrom();

    /**
     * Get zip range ending point
     *
     * @return int
     */
    public function getTo();
}
