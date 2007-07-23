<?php
class Mage_Core_Controller_Varien_Router_Standard extends Mage_Core_Controller_Varien_Router_Abstract
{
    protected $_modules = array();
    protected $_dispatchData = array();
    
    public function match(Zend_Controller_Request_Http $request)
    {
        $p = explode('/', trim($request->getPathInfo(), '/'));
        
        $front = $this->getFront();
        
        // get module name
        if ($request->getModuleName()) {
            $module = $request->getModuleName();
        } else {
            $module = !empty($p[0]) ? $p[0] : $front->getDefault('module');
        }
        $realModule = $this->getRealModuleName($module);
        if (!$realModule) {
            if ($moduleFrontName = array_search($module, $this->_modules)) {
                $realModule = $module;
                $module = $moduleFrontName;
            } else {
                return false;
            }
        }
        
        // get controller name
        if ($request->getControllerName()) {
            $controller = $request->getControllerName();
        } else {
            $controller = !empty($p[1]) ? $p[1] : $front->getDefault('controller');
        }
        $controllerFileName = $this->getControllerFileName($realModule, $controller);
        if (!$controllerFileName || !is_readable($controllerFileName)) {
        	$controller = 'index';
            $action = 'noroute';
            $controllerFileName = $this->getControllerFileName($realModule, $controller);
        }
        $controllerClassName = $this->getControllerClassName($realModule, $controller);
        if (!$controllerClassName) {
        	$controller = 'index';
            $action = 'noroute';
            $controllerFileName = $this->getControllerFileName($realModule, $controller);
        }
        
        // get action name
        if (empty($action)) {
	        if ($request->getActionName()) {
	            $action = $request->getActionName();
	        } else {
	            $action = !empty($p[2]) ? $p[2] : $front->getDefault('action');
	        }
        }
        
        // include controller file if needed
        if (!class_exists($controllerClassName, false)) {
            include $controllerFileName;
        }
        // instantiate controller class
        $controllerInstance = new $controllerClassName($request, $front->getResponse());

        // set values only after all the checks are done
        $request->setModuleName($module);
        $request->setControllerName($controller);
        $request->setActionName($action);
        
        // set parameters from pathinfo
        for ($i=3, $l=sizeof($p); $i<$l; $i+=2) {
            $request->setParam($p[$i], isset($p[$i+1]) ? $p[$i+1] : '');
        }

        // dispatch action
        $request->setDispatched(true);
        $controllerInstance->dispatch($action);
        
        return true;
    }
    
    public function addModule($frontName, $moduleName)
    {
        $this->_modules[$frontName] = $moduleName;
        return $this;
    }
    
    public function getRealModuleName($frontName)
    {
        if (isset($this->_modules[$frontName])) {
            return $this->_modules[$frontName];
        }
        return false;
    }
    
    public function getControllerFileName($realModule, $controller)
    {
        $file = Mage::getModuleDir('controllers', $realModule);
        $file .= DS.uc_words($controller, DS).'Controller.php';
        return $file;
    }
    
    public function getControllerClassName($realModule, $controller)
    {
        $class = $realModule.'_'.uc_words($controller).'Controller';
        return $class;
    }
    
    public function getUrl($routeName, $params=array())
    {
        static $reservedKeys = array('module'=>1, 'controller'=>1, 'action'=>1, 'array'=>1);
        
        if (is_string($params)) {
            $paramsArr = explode('/', $params);
            $params = array('controller'=>$paramsArr[0], 'action'=>$paramsArr[1]);
        }
        
        $url = Mage::getBaseUrl($params);

        $url .= $routeName.'/';
        
        if (!empty($params)) {
            if (!empty($params['_current'])) {
                if ($params['_current']===true) {
                    $params = array_merge($this->getFront()->getRequest()->getParams(), $params);
                } elseif (is_array($params['_current'])) {
                    foreach ($params['_current'] as $param) {
                        $params[$param] = $this->getFront()->getRequest()->getParam($param);
                    }
                }
            }
            $paramsStr = '';
            foreach ($params as $key=>$value) {
                if (!isset($reservedKeys[$key]) && '_'!==$key{0} && !empty($value)) {
                    $paramsStr .= $key.'/'.$value.'/';
                }
            }
            
            if (empty($params['controller']) && !empty($paramsStr)) {
                $params['controller'] = $this->getFront()->getDefault('controller');
            }
            $url .= empty($params['controller']) ? '' : $params['controller'].'/';
            
            if (empty($params['action']) && !empty($paramsStr)) {
                $params['action'] = $this->getFront()->getDefault('action');
            }
            $url .= empty($params['action']) ? '' : $params['action'].'/';
            
            $url .= $paramsStr;
            
            $url .= empty($params['array']) ? '' : '?' . http_build_query($params['array']);
        }

        return $url;
    }
}