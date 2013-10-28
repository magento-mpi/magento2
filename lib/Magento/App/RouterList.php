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
namespace Magento\App;

class RouterList implements RouterListInterface
{
    /**
     * @var \Magento\ObjectManager
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
     * @param \Magento\ObjectManager $objectManager
     * @param array $routerList
     */
    public function __construct(\Magento\ObjectManager $objectManager, array $routerList)
    {
        $this->_objectManager = $objectManager;
        $this->_routerList = $routerList;
        $this->_routerList = array_filter($routerList, function($item) {
            return (!isset($item['disable']) || !$item['disable']) && $item['instance'];
        });
        uasort($this->_routerList, array($this, '_compareRoutersSortOrder'));
    }

    /**
     * Retrieve router instance by id
     *
     * @param string $routerId
     * @return RouterInterface
     */
    protected function _getRouterInstance($routerId)
    {
        if (!isset($this->_routerList[$routerId]['object'])) {
            $this->_routerList[$routerId]['object'] = $this->_objectManager->create(
                $this->_routerList[$routerId]['instance']
            );
        }
        return $this->_routerList[$routerId]['object'];
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Return the current element
     * @link http://php.net/manual/en/iterator.current.php
     * @return RouterInterface
     */
    public function current()
    {
        return $this->_getRouterInstance(key($this->_routerList));
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Move forward to next element
     * @link http://php.net/manual/en/iterator.next.php
     * @return void Any returned value is ignored.
     */
    public function next()
    {
        next($this->_routerList);
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Return the key of the current element
     * @link http://php.net/manual/en/iterator.key.php
     * @return mixed scalar on success, or null on failure.
     */
    public function key()
    {
        key($this->_routerList);
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Checks if current position is valid
     * @link http://php.net/manual/en/iterator.valid.php
     * @return boolean The return value will be casted to boolean and then evaluated.
     * Returns true on success or false on failure.
     */
    public function valid()
    {
        return !!current($this->_routerList);
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Rewind the Iterator to the first element
     * @link http://php.net/manual/en/iterator.rewind.php
     * @return void Any returned value is ignored.
     */
    public function rewind()
    {
        reset($this->_routerList);
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
        if ((int)$routerData1['sortOrder'] == (int)$routerData2['sortOrder']) {
            return 0;
        }

        if ((int)$routerData1['sortOrder'] < (int)$routerData2['sortOrder']) {
            return -1;
        } else {
            return 1;
        }
    }

    /**
     * Get router by route
     *
     * @param string $routeId
     * @return RouterInterface
     */
    public function getRouterByRoute($routeId)
    {
        // empty route supplied - return base url
        if (empty($routeId)) {
            return $this->_getRouterInstance('standard');
        }

        if (isset($this->_routerList['admin'])) {
            $router = $this->_getRouterInstance('admin');
            if ($router->getFrontNameByRoute($routeId)) {
                return $router;
            }
        }

        if (isset($this->_routerList['standard'])) {
            $router = $this->_getRouterInstance('standard');
            if ($router->getFrontNameByRoute($routeId)) {
                return $router;
            }
        }

        if (isset($this->_routerList[$routeId])) {
            $router = $this->_getRouterInstance($routeId);
            if ($router->getFrontNameByRoute($routeId)) {
                return $router;
            }
        }

        return $this->_getRouterInstance('default');
    }

    /**
     * Get router by frontName
     *
     * @param string $frontName
     * @return RouterInterface
     */
    public function getRouterByFrontName($frontName)
    {
        // empty route supplied - return base url
        if (empty($frontName)) {
            return $this->_getRouterInstance('standard');
        }

        if (isset($this->_routerList['admin'])) {
            $router = $this->_getRouterInstance('admin');
            if ($router->getRouteByFrontname($frontName)) {
                return $router;
            }
        }

        if (isset($this->_routerList['standard'])) {
            $router = $this->_getRouterInstance('standard');
            if ($router->getRouteByFrontname($frontName)) {
                return $router;
            }
        }

        return $this->_getRouterInstance('default');
    }
}
