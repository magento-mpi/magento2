<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */


namespace Magento\Framework\Api\Data;

interface SimpleObjectInterface
{
    /**
     * Convert object to array
     *
     * @return array
     */
    public function __toArray();
}
