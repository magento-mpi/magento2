<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\Model\Cache;

interface TypeListInterface
{
    /**
     * Get information about all declared cache types
     *
     * @return array
     */
    public function getTypes();

    /**
     * Get array of all invalidated cache types
     *
     * @return array
     */
    public function getInvalidated();

    /**
     * Mark specific cache type(s) as invalidated
     *
     * @param string|array $typeCode
     */
    public function invalidate($typeCode);

    /**
     * Clean cached data for specific cache type
     *
     * @param string $typeCode
     */
    public function cleanType($typeCode);
}
