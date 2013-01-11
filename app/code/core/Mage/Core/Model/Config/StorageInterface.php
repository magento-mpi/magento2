<?php
/**
 * Application config storage interface
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
interface Mage_Core_Model_Config_StorageInterface
{
    /**
     * Get loaded configuration
     *
     * @param bool $useCache
     * @return mixed
     */
    public function getConfiguration($useCache = true);

    /**
     * Remove configuration cache
     */
    public function removeCache();
}
