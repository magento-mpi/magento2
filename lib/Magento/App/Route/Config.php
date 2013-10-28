<?php
/**
 * Routes configuration model
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\App\Route;

class Config implements \Magento\App\Route\ConfigInterface
{
    /**
     * @var \Magento\App\Route\Config\Reader
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
     * @var \Magento\Config\ScopeInterface
     */
    protected $_configScope;

    /**
     * @param Config\Reader $reader
     * @param \Magento\Config\CacheInterface $cache
     * @param \Magento\Config\ScopeInterface $configScope
     * @param string $cacheId
     */
    public function __construct(
        Config\Reader $reader,
        \Magento\Config\CacheInterface $cache,
        \Magento\Config\ScopeInterface $configScope,
        $cacheId = 'RoutesConfig'
    ) {
        $this->_reader = $reader;
        $this->_cache = $cache;
        $this->_cacheId = $cacheId;
        $this->_configScope = $configScope;
    }

    /**
     * Fetch routes from configs by area code and router id
     *
     * @param string $routerId
     * @return array
     */
    public function getRoutes($routerId)
    {
        $cacheId = $this->_configScope->getCurrentScope() . '::'  . $this->_cacheId . '-' . $routerId;
        $cachedRoutes = unserialize($this->_cache->load($cacheId));
        if (is_array($cachedRoutes)) {
            return $cachedRoutes;
        }

        $routes = array();
        $areaConfig = $this->_reader->read($this->_configScope->getCurrentScope());
        if (array_key_exists($routerId, $areaConfig)) {
            $routes = $areaConfig[$routerId]['routes'];
            $this->_cache->save(serialize($routes), $cacheId);
        }

        return $routes;
    }
}
