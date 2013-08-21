<?php
/**
 * Router list
 * Used as a container for list of routers
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Core_Model_RouterList
{
    /**
     * @var Magento_ObjectManager
     */
    protected $_objectManager;

    /**
     * List of routers
     *
     * @var array
     */
    protected $_routerList;

    /**
     * List of active routers objects
     *
     * @var array
     */
    protected $_activeRouters = array();

    /**
     * @param Magento_ObjectManager $objectManager
     * @param array $routerList
     */
    public function __construct(Magento_ObjectManager $objectManager, array $routerList)
    {
        $this->_objectManager = $objectManager;
        $this->_routerList = $routerList;
    }

    /**
     * Get list of active routers
     * sorted by sortOrder
     *
     * @return array
     */
    public function getRouters()
    {
        if (empty($this->_activeRouters)) {
            $this->_loadRouters();
        }

        return $this->_activeRouters;
    }

    protected function _loadRouters()
    {
        foreach ($this->_getSortedRouterList() as $routerCode => $routerData)
        {
            if (!$routerData['disable'] && $routerData['class']) {
                $this->_activeRouters[$routerCode] = $this->_objectManager->create($routerData['class']);
            }
        }

        return $this->_activeRouters;
    }

    /**
     * Sort routerList by sortOrder
     *
     * @return array
     */
    protected function _getSortedRouterList()
    {
        usort($this->_routerList, array($this, '_compareItemsSortOrder'));
        return $this->_routerList;
    }

    /**
     * Compare routers sortOrder
     *
     * @param array $routerData1
     * @param array $routerData2
     * @return int
     */
    protected function _compareRoutersSortOrder($routerData1, $routerData2)
    {
        if ($routerData1['sortOrder'] == $routerData1['sortOrder']) return 0;
        return ($routerData1['sortOrder'] < $routerData2['sortOrder']) ? 1 : -1;
    }

    /**
     * Get router by route
     *
     * @param string $routeId
     * @return Mage_Core_Controller_Varien_Router_Abstract
     */
    public function getRouterByRoute($routeId)
    {
        $activeRouters = $this->_loadRouters();
        // empty route supplied - return base url
        if (empty($routeId)) {
            return $activeRouters['standard'];
        }

        if (isset($activeRouters['admin'])) {
            $router = $activeRouters['admin'];
            if ($router->getFrontNameByRoute($routeId)) {
                return $router;
            }
        }

        if (isset($activeRouters['standard'])) {
            $router = $activeRouters['standard'];
            if ($router->getFrontNameByRoute($routeId)) {
                return $router;
            }
        }

        if (isset($activeRouters[$routeId])) {
            $router = $activeRouters[$routeId];
            if ($router->getFrontNameByRoute($routeId)) {
                return $router;
            }
        }

        $router = $activeRouters['default'];;
        return $router;
    }

    /**
     * Get router by frontName
     *
     * @param string $frontName
     * @return Mage_Core_Controller_Varien_Router_Abstract
     */
    public function getRouterByFrontName($frontName)
    {
        $activeRouters = $this->_loadRouters();

        // empty route supplied - return base url
        if (empty($frontName)) {
            return $activeRouters['standard'];
        }

        if (isset($activeRouters['admin'])) {
            $router = $activeRouters['admin'];
            if ($router->getRouteByFrontname($frontName)) {
                return $router;
            }
        }

        if (isset($activeRouters['standard'])) {
            $router = $activeRouters['standard'];
            if ($router->getRouteByFrontname($frontName)) {
                return $router;
            }
        }

        $router = $activeRouters['default'];;
        return $router;
    }
}