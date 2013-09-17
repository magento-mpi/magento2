<?php
/**
 * {license_notice}
 * 
 * @copyright {copyright}
 * @license   {license_link}
 */

interface Magento_ObjectManager_ConfigCache
{
    /**
     * Retrieve configuration from cache
     *
     * @param string $key
     * @return array
     */
    public function get($key);

    /**
     * Save config to cache
     *
     * @param array $config
     * @param string $key
     */
    public function save(array $config, $key);
}
