<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Core_Model_Router_Config implements Mage_Core_Model_Router_ConfigInterface
{
    /**
     * @param Mage_Core_Model_Router_Config_Reader $reader
     * @param Magento_Config_CacheInterface $cache
     * @param string $cacheId
     */
    public function __construct(
        Mage_Core_Model_Router_Config_Reader $reader,
        Magento_Config_CacheInterface $cache,
        $cacheId = 'RouterConfig'
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
        if (!array_key_exists($routerId, $areaConfig)) {
            return $routes;
        }
        $routes = $areaConfig[$routerId]['routes'];
        $this->_cache->put($routes, $areaCode, $cacheId);

        return $routes;

    }
}
