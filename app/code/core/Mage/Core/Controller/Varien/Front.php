<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Core
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


class Mage_Core_Controller_Varien_Front
{
    /**
     * Request object
     *
     * @var Zend_Controller_Request_Http
     */
    protected $_request;

    /**
     * Response object
     *
     * @var Zend_Controller_Response_Http
     */
    protected $_response;


    protected $_defaults = array();

    /**
     * Available routers array
     *
     * @var array
     */
    protected $_routers = array();

    protected $_urlCache = array();

    public function setDefault($key, $value=null)
    {
        if (is_array($key)) {
            $this->_defaults = $key;
        } else {
            $this->_defaults[$key] = $value;
        }
        return $this;
    }

    public function getDefault($key=null)
    {
        if (is_null($key)) {
            return $this->_defaults;
        } elseif (isset($this->_defaults[$key])) {
            return $this->_defaults[$key];
        }
        return false;
    }

    /**
     * Retrieve request object
     *
     * @return Zend_Controller_Request_Http
     */
    public function getRequest()
    {
        if (empty($this->_request)) {
            $this->_request = new Zend_Controller_Request_Http();
        }
        return $this->_request;
    }

    /**
     * Retrieve response object
     *
     * @return Zend_Controller_Response_Http
     */
    public function getResponse()
    {
        if (empty($this->_response)) {
            $this->_response = new Zend_Controller_Response_Http();
        }
        return $this->_response;
    }

    /**
     * Adding new router
     *
     * @param   string $name
     * @param   Mage_Core_Controller_Varien_Router_Abstract $router
     * @return  Mage_Core_Controller_Varien_Front
     */
    public function addRouter($name, Mage_Core_Controller_Varien_Router_Abstract $router)
    {
        $router->setFront($this);
        $this->_routers[$name] = $router;
        return $this;
    }

    /**
     * Retrieve router by name
     *
     * @param   string $name
     * @return  Mage_Core_Controller_Varien_Router_Abstract
     */
    public function getRouter($name)
    {
        if (isset($this->_routers[$name])) {
            return $this->_routers[$name];
        }
        return false;
    }

    public function init()
    {
        Mage::dispatchEvent('beforeFrontRun');

        Varien_Profiler::start('ctrl/init');

        // init admin modules router
        $admin = new Mage_Core_Controller_Varien_Router_Admin();
        $admin->collectRoutes('admin', 'admin');
        $this->addRouter('admin', $admin);

        // init standard frontend modules router
        $standard = new Mage_Core_Controller_Varien_Router_Standard();
        $standard->collectRoutes('frontend', 'standard');
        $this->addRouter('standard', $standard);

        // init custom routers
        Mage::dispatchEvent('initControllerRouters', array('front'=>$this));

        // init default router (articles and 404)
        $default = new Mage_Core_Controller_Varien_Router_Default();
        $this->addRouter('default', $default);

        Varien_Profiler::stop('ctrl/init');

        return $this;
    }

    public function dispatch()
    {
        Varien_Profiler::start('ctrl/dispatch');

        $request = $this->getRequest();
        $request->setPathInfo()->setDispatched(false);

        $this->rewrite();

        $i = 0;
        while (!$request->isDispatched() && $i++<100) {
            foreach ($this->_routers as $router) {
                if ($router->match($this->getRequest())) {
                    break;
                }
            }
        }

        Varien_Profiler::stop('ctrl/dispatch');

        Varien_Profiler::start('ctrl/response');
        $this->getResponse()->sendResponse();
        Varien_Profiler::stop('ctrl/response');

        return $this;
    }
/*
    public function getUrl($route='', $params=array())
    {
        if (!isset($params['_current'])) {
            $cacheKey = md5($route.serialize($params));
        }
        if (isset($cacheKey) && isset($this->_urlCache[$cacheKey])) {
            return $this->_urlCache[$cacheKey];
        }

        // no route return base url
        if (empty($route)) {
            return Mage::getBaseUrl();
        }


        if (is_string($route)) {
            // parse string route
            $request = $this->getRequest();

            $p = explode('/', $route);
            $routeName = $p[0]==='*' ? $request->getModuleName() : $p[0];
            $paramsArr = array('module'=>$routeName);
            if (isset($p[1])) {
                $paramsArr['controller'] = $p[1]==='*' ? $request->getControllerName() : $p[1];
                if (isset($p[2])) {
                    $paramsArr['action'] = $p[2]==='*' ? $request->getActionName() : $p[2];
                    for ($i=3, $l=sizeof($p); $i<$l; $i+=2) {
                        $paramsArr[$p[$i]] = isset($p[$i+1]) ? $p[$i+1] : '';
                    }
                }
            }
        } elseif (is_array($route)) {
            // parse array route
            $routeName = $route['module'];
            $paramsArr = $route;
        } else {
            // unknown route format
            $url = '';
            if (isset($cacheKey)) {
                $this->_urlCache[$cacheKey] = $url;
            }
            return $url;
        }
        // merge with optional params
        $paramsArr = array_merge($paramsArr, $params);
        $router = $this->getRouterByRoute($routeName);
        $url = $router->getUrl($routeName, $paramsArr);
        if (isset($cacheKey)) {
            $this->_urlCache[$cacheKey] = $url;
        }
        return $url;
    }
*/
    public function getRouterByRoute($routeName)
    {
        // empty route supplied - return base url
        if (empty($routeName)) {
            $router = $this->getRouter('standard');
        } elseif ($this->getRouter('admin')->getRealModuleName($routeName)) {
            // try standard router url assembly
            $router = $this->getRouter('admin');
        } elseif ($this->getRouter('standard')->getRealModuleName($routeName)) {
            // try standard router url assembly
            $router = $this->getRouter('standard');
        } elseif ($router = $this->getRouter($routeName)) {
            // try custom router url assembly
        } else {
            // get default router url
            $router = $this->getRouter('default');
        }

        return $router;
    }

    public function rewrite()
    {
    	$request = $this->getRequest();
    	$config = Mage::getConfig()->getNode('global/rewrite');
    	if (!$config) {
    		return;
    	}
    	foreach ($config->children() as $rewrite) {
    		$from = (string)$rewrite->from;
    		$to = (string)$rewrite->to;
    		if (empty($from) || empty($to)) {
    			continue;
    		}
    		$pathInfo = preg_replace($from, $to, $request->getPathInfo());
    		$request->setPathInfo($pathInfo);
    	}
    }
}