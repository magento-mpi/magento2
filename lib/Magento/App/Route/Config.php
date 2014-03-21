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

class Config implements ConfigInterface
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
     * @var \Magento\App\AreaList
     */
    protected $_areaList;

    /**
     * @var array
     */
    protected $_routes;

    /**
     * @param Config\Reader $reader
     * @param \Magento\Config\CacheInterface $cache
     * @param \Magento\Config\ScopeInterface $configScope
     * @param \Magento\App\AreaList $areaList
     * @param string $cacheId
     */
    public function __construct(
        Config\Reader $reader,
        \Magento\Config\CacheInterface $cache,
        \Magento\Config\ScopeInterface $configScope,
        \Magento\App\AreaList $areaList,
        $cacheId = 'RoutesConfig'
    ) {
        $this->_reader = $reader;
        $this->_cache = $cache;
        $this->_cacheId = $cacheId;
        $this->_configScope = $configScope;
        $this->_areaList = $areaList;
    }

    /**
     * Fetch routes from configs by area code and router id
     *
     * @param string $scope
     * @return array
     */
    protected function _getRoutes($scope = null)
    {
        $scope = $scope ?: $this->_configScope->getCurrentScope();
        if (isset($this->_routes[$scope])) {
            return $this->_routes[$scope];
        }
        $cacheId = $scope . '::' . $this->_cacheId;
        $cachedRoutes = unserialize($this->_cache->load($cacheId));
        if (is_array($cachedRoutes)) {
            $this->_routes[$scope] = $cachedRoutes;
            return $cachedRoutes;
        }

        $routers = $this->_reader->read($scope);
        $routes = $routers[$this->_areaList->getDefaultRouter($scope)]['routes'];
        $this->_cache->save(serialize($routes), $cacheId);
        $this->_routes[$scope] = $routes;
        return $routes;
    }

    /**
     * Retrieve route front name
     *
     * @param string $routeId
     * @param null $scope
     * @return string
     */
    public function getRouteFrontName($routeId, $scope = null)
    {
        $routes = $this->_getRoutes($scope);
        return isset($routes[$routeId]) ? $routes[$routeId]['frontName'] : $routeId;
    }

    /**
     * @param string $frontName
     * @param string $scope
     * @return bool|int|string
     */
    public function getRouteByFrontName($frontName, $scope = null)
    {
        foreach ($this->_getRoutes($scope) as $routeId => $routeData) {
            if ($routeData['frontName'] == $frontName) {
                return $routeId;
            }
        }

        return false;
    }

    /**
     * @param string $frontName
     * @param string $scope
     * @return string[]
     */
    public function getModulesByFrontName($frontName, $scope = null)
    {
        $routes = $this->_getRoutes($scope);
        $modules = array();
        foreach ($routes as $routeData) {
            if ($routeData['frontName'] == $frontName && isset($routeData['modules'])) {
                $modules = $routeData['modules'];
                break;
            }
        }

        return array_unique($modules);
    }
}
