<?php
/**
 * Initial configuration data container. Provides interface for reading initial config values
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Core_Model_Config_Initial
{
    /**
     * @param Mage_Core_Model_Config_Initial_Reader $reader
     * @param Mage_Core_Model_Cache_Type_Config $cache
     * @param string $cacheId
     */
    public function __construct(
        Mage_Core_Model_Config_Initial_Reader $reader,
        Mage_Core_Model_Cache_Type_Config $cache,
        $cacheId = 'initial_config'
    ) {
        $data = $cache->load($cacheId);
        if (!$data) {
            $data = $reader->read();
            $cache->save(serialize($data), $cacheId);
        } else {
            $data = unserialize($data);
        }
        $this->_data = $data;
    }

    /**
     * Get default config
     *
     * @return array
     */
    public function getDefault()
    {
        return $this->_data['default'];
    }

    /**
     * Retrieve store initial config by code
     *
     * @param string $code
     * @return array
     */
    public function getStore($code)
    {
        return isset($this->_data['stores'][$code]) ? $this->_data['stores'][$code] : array();
    }

    /**
     * Retrieve website initial config by code
     *
     * @param string $code
     * @return array
     */
    public function getWebsite($code)
    {
        return isset($this->_data['websites'][$code]) ? $this->_data['websites'][$code] : array();
    }
}
