<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webapi\Model;

use Magento\Webapi\Model\Cache\Type;
use Magento\Webapi\Model\Config\Reader;
use Zend\Code\Reflection\ClassReflection;

/**
 * Web API Config Model.
 *
 * This is a parent class for storing information about Web API. Most of it is needed by REST.
 */
class Config
{
    const CACHE_ID = 'webapi';
    const DATA_INTERFACE_METHODS = 'dataInterfaceMethods';

    /**
     * Pattern for Web API interface name.
     */
    const SERVICE_CLASS_PATTERN = '/^(.+?)\\\\(.+?)\\\\Service\\\\(V\d+)+(\\\\.+)Interface$/';

    /**
     * @var \Magento\Framework\App\Cache\Type\Config
     */
    protected $_configCacheType;

    /**
     * @var Reader
     */
    protected $_configReader;

    /**
     * Module configuration reader
     *
     * @var \Magento\Framework\Module\Dir\Reader
     */
    protected $_moduleReader;

    /**
     * @var array
     */
    protected $_services;

    /**
     * @var array
     */
    protected $dataInterfaceMethodsMap = [];

    /**
     * @param Type $configCacheType
     * @param Reader $configReader
     */
    public function __construct(Type $configCacheType, Reader $configReader)
    {
        $this->_configCacheType = $configCacheType;
        $this->_configReader = $configReader;
    }

    /**
     * Return services loaded from cache if enabled or from files merged previously
     *
     * @return array
     */
    public function getServices()
    {
        if (null === $this->_services) {
            $services = $this->_loadFromCache(self::CACHE_ID);
            if ($services && is_string($services)) {
                $this->_services = unserialize($services);
            } else {
                $this->_services = $this->_configReader->read();
                $this->_saveToCache(serialize($this->_services), self::CACHE_ID);
            }
        }
        return $this->_services;
    }

    /**
     * Return Data Interface methods loaded from cache
     *
     * @param string $dataInterface Data Interface name
     * @return array
     */
    public function getDataInterfaceMethods($dataInterface)
    {
        $key = self::DATA_INTERFACE_METHODS . "-" . md5($dataInterface);
        if (!isset($this->dataInterfaceMethodsMap[$key])) {
            $methods = $this->_loadFromCache($key);
            if ($methods && is_string($methods)) {
                $this->dataInterfaceMethodsMap[$key] = unserialize($methods);
            } else {
                $class = new ClassReflection($dataInterface);
                $this->dataInterfaceMethodsMap[$key] = $class->getMethods();
                $this->_saveToCache(serialize($this->dataInterfaceMethodsMap[$key]), $key);
            }
        }
        return $this->dataInterfaceMethodsMap[$key];
    }

    /**
     * Load from cache
     *
     * @param string $cacheId cache to look up from
     * @return string|bool
     */
    protected function _loadFromCache($cacheId)
    {
        return $this->_configCacheType->load($cacheId);
    }

    /**
     * Save into the cache
     *
     * @param string $data serialized version of the webapi registry
     * @param string $cacheId save cache with this id
     * @return $this
     */
    protected function _saveToCache($data, $cacheId)
    {
        $this->_configCacheType->save($data, $cacheId, array(\Magento\Webapi\Model\Cache\Type::CACHE_TAG));
        return $this;
    }
}
