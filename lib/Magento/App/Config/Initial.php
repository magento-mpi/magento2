<?php
/**
 * Initial configuration data container. Provides interface for reading initial config values
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
namespace Magento\App\Config;

class Initial
{
    /**
     * Cache identifier used to store initial config
     */
    const CACHE_ID  = 'initial_config';

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
     * @param \Magento\App\Config\Initial\Reader $reader
     * @param \Magento\App\Cache\Type\Config $cache
     */
    public function __construct(
        \Magento\App\Config\Initial\Reader $reader,
        \Magento\App\Cache\Type\Config $cache
    ) {
        $data = $cache->load(self::CACHE_ID);
        if (!$data) {
            $data = $reader->read();
            $cache->save(serialize($data), self::CACHE_ID);
        } else {
            $data = unserialize($data);
        }
        $this->_data = $data['data'];
        $this->_metadata = $data['metadata'];
    }

    /**
     * Get initial data by given scope
     *
     * @return mixed
     */
    public function getData($scope)
    {
        list($scopeType, $scopeCode) = array_pad(explode('|', $scope), 2, null);

        if ('default' == $scopeType) {
            return isset($this->_data[$scopeType]) ? $this->_data[$scopeType] : array();
        } elseif ($scopeCode) {
            return isset($this->_data[$scopeType][$scopeCode]) ? $this->_data[$scopeType][$scopeCode] : array();
        }
        return array();
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
