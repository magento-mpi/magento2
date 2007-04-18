<?php

class Mage_Core_Controller_Front_Router
{
    protected $_config = null;
    
    public function __construct($config)
    {
        $this->_config = $config;
    }
    
    public function getConfig()
    {
        return $this->_config;
    }
    
    public function addRoutes($router)
    {
        $frontName = $this->_config->getName();
        $routeMatch = $frontName.'/:controller/:action/*';
        $moduleName = (string)$this->_config->module;
        $routeParams = array('module'=>$moduleName, 'controller'=>'index', 'action'=>'index', '_frontName'=>$frontName);
        $route = new Zend_Controller_Router_Route($routeMatch, $routeParams);
        $router->addRoute($moduleName, $route);
        
        return $this;
    }
    
    public function getUrl($params=array())
    {
        static $reservedKeys = array('module'=>1, 'controller'=>1, 'action'=>1);
        
        if (is_string($params)) {
            $paramsArr = explode('/', $params);
            $params = array('controller'=>$paramsArr[0], 'action'=>$paramsArr[1]);
        }
        
        $url = Mage::getConfig()->getBaseUrl($params);

        if (!empty($params['frontName'])) {
            $url .= $params['frontName'].'/';
        } else {
            $url .= $this->_config->getName().'/';
        }
        
        if (!empty($params)) {
            $paramsStr = '';
            foreach ($params as $key=>$value) {
                if (!isset($reservedKeys[$key]) && '_'!==$key{0} && !empty($value)) {
                    $paramsStr .= $key.'/'.$value.'/';
                }
            }
            
            if (empty($params['controller']) && !empty($paramsStr)) {
                $params['controller'] = 'index';
            }
            $url .= empty($params['controller']) ? '' : $params['controller'].'/';
            
            if (empty($params['action']) && !empty($paramsStr)) {
                $params['action'] = 'index';
            }
            $url .= empty($params['action']) ? '' : $params['action'].'/';
            
            $url .= $paramsStr;
        }
        
        return $url;
    }
}