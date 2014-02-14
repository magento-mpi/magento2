<?php
/**
 * Cache configuration model. Provides cache configuration data to the application
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Cache;

interface ConfigInterface
{
    /**
     * Get configuration of all cache types
     *
     * @return array
     */
    public function getTypes();

    /**
     * Get configuration of specified cache type
     *
     * @param string $type
     * @return array
     */
    public function getType($type);
}
