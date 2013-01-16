<?php
/**
 * Application config storage
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Core_Model_Config_Storage extends Mage_Core_Model_Config_StorageAbstract
{
     /**
     * Retrieve application configuration
     *
     * @param bool $useCache
     * @return Mage_Core_Model_ConfigInterface
     */
    public function getConfiguration($useCache = true)
    {
        $config = $useCache ? $this->_cache->load() : false;
        if (false === $config) {
            $config = $this->_configFactory->create('<config/>');
            $this->_loader->load($config);
            if ($useCache) {
                $this->_cache->save($config);
            }
        }
        return $config;
    }

    /**
     * Remove configuration cache
     */
    public function removeCache()
    {
        $this->_cache->clean();
    }
}
