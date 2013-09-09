<?php
/**
 * Initial configuration data container. Provides interface for reading initial config values
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Core_Model_Config_Initial
{
    /**
     * Config data
     *
     * @var array
     */
    protected $_data = array();

    /**
     * Config metadata
     *
     * @var array
     */
    protected $_metadata = array();

    /**
     * @param Magento_Core_Model_Config_Initial_Reader $reader
     * @param Magento_Core_Model_Cache_Type_Config $cache
     * @param string $cacheId
     */
    public function __construct(
        Magento_Core_Model_Config_Initial_Reader $reader,
        Magento_Core_Model_Cache_Type_Config $cache,
        $cacheId = 'initial_config'
    ) {
        $data = $cache->load($cacheId);
        if (!$data) {
            $data = $reader->read();
            $cache->save(serialize($data), $cacheId);
        } else {
            $data = unserialize($data);
        }
        $this->_data = $data['data'];
        $this->_metadata = $data['metadata'];
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

    /**
     * Get configuration metadata
     *
     * @return array
     */
    public function getMetadata()
    {
        return $this->_metadata;
    }
}
