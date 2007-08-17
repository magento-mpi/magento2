<?php

class Mage_Core_Controller_Varien_Front
{
    protected $_request;
    
    protected $_response;
    
    protected $_defaults = array();
    
    protected $_routers = array();

    protected $_urlCache = array();
    
    protected $_storeCode;
    
     public function setStoreCode($storeCode)
     {
     	$this->_storeCode = $storeCode;
     	return $this;
     }
     
     public function getStoreCode()
     {
     	return $this->_storeCode;
     }

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
    
    public function getRequest()
    {
        if (empty($this->_request)) {
            $this->_request = new Zend_Controller_Request_Http();
        }
        return $this->_request;
    }
    
    public function getResponse()
    {
        if (empty($this->_response)) {
            $this->_response = new Zend_Controller_Response_Http();
        }
        return $this->_response;
    }
    
    public function addRouter($name, Mage_Core_Controller_Varien_Router_Abstract $router)
    {
        $router->setFront($this);
        $this->_routers[$name] = $router;
        return $this;
    }
    
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
        $this->addRouter('admin', $admin);
        $this->collectRouters('admin', 'admin', $admin);
        
        // init standard frontend modules router
        $standard = new Mage_Core_Controller_Varien_Router_Standard();
        $this->addRouter('standard', $standard);
        $this->collectRouters('frontend', 'standard', $standard);
        
        // init custom routers
        Mage::dispatchEvent('initControllerRouters', array('front'=>$this));

        // init default router (articles and 404)
        $default = new Mage_Core_Controller_Varien_Router_Default();
        $this->addRouter('default', $default);
        
        Varien_Profiler::stop('ctrl/init');
        
        return $this;
    }
    
    public function collectRouters($configArea, $useRouterName, Mage_Core_Controller_Varien_Router_Abstract $parentRouter)
    {
        $routers = Mage::getConfig()->getNode($configArea.'/routers')->children();
        foreach ($routers as $routerName=>$routerConfig) {
            $use = (string)$routerConfig->use;
            
            if ($use===$useRouterName) {
                $module = (string)$routerConfig->args->module;
                $frontName = (string)$routerConfig->args->frontName;
                $parentRouter->addModule($frontName, $module);
            }
        }
    }
    
    public function dispatch()
    {
        Varien_Profiler::start('ctrl/dispatch');
        
        $request = $this->getRequest();
        $request->setPathInfo()->setDispatched(false);

        $i = 0;
        while (!$request->isDispatched() && $i++<100) {
#Mage::log('DISPATCH: '.$request->getModuleName().'/'.$request->getControllerName().'/'.$request->getActionName());
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
        
        // empty route supplied - return base url
        if (empty($routeName)) {
            $url = Mage::getBaseUrl();
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
        $url = $router->getUrl($routeName, $paramsArr);
        if (isset($cacheKey)) {
            $this->_urlCache[$cacheKey] = $url;
        }
        return $url;
    }
}