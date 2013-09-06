<?php
/**
 * Routes configuration model
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Core_Model_Route_Config implements Magento_Core_Model_Route_ConfigInterface
{
    /**
     * @var Magento_Core_Model_Route_Config_Reader
     */
    protected $_reader;

    /**
     * @var \Magento\Cache\FrontendInterface
     */
    protected $_cache;

    /**
     * @var string
     */
    protected $_cacheId;

    /**
     * @param Magento_Core_Model_Route_Config_Reader $reader
     * @param Magento_Config_CacheInterface $cache
     * @param string $cacheId
     */
    public function __construct(
        Magento_Core_Model_Route_Config_Reader $reader,
        Magento_Config_CacheInterface $cache,
        $cacheId = 'RoutesConfig'
    ) {
        $this->_reader = $reader;
        $this->_cache = $cache;
        $this->_cacheId = $cacheId;
    }

    /**
     * Fetch routes from configs by area code and router id
     *
     * @param string $areaCode
     * @param string $routerId
     * @return array
     */
    public function getRoutes($areaCode, $routerId)
    {
        $cacheId = $this->_cacheId . '-' . $routerId;
        $cachedRoutes = $this->_cache->get($areaCode, $cacheId);
        if (is_array($cachedRoutes)) {
            return $cachedRoutes;
        }

        $routes = array();
        $areaConfig = $this->_reader->read($areaCode);
        if (array_key_exists($routerId, $areaConfig)) {
            $routes = $areaConfig[$routerId]['routes'];
            $this->_cache->put($routes, $areaCode, $cacheId);
        }

        return $routes;
    }
}
