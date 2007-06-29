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
            return false;
        }
        $controllerClassName = $this->getControllerClassName($realModule, $controller);
        if (!$controllerClassName) {
            return false;
        }
        
        // get action name
        if ($request->getActionName()) {
            $action = $request->getActionName();
        } else {
            $action = !empty($p[2]) ? $p[2] : $front->getDefault('action');
        }
        $actionMethodName = $this->getActionMethodName($action);
        
        // include controller file if needed
        if (!class_exists($controllerClassName, false)) {
            include $controllerFileName;
        }
        // instantiate controller class
        $controllerInstance = new $controllerClassName($request, $front->getResponse());
        
        if (!is_callable(array($controllerInstance, $actionMethodName))) {
            return false;
        }

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
        $controllerInstance->dispatch($actionMethodName);
        
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
    
    public function getActionMethodName($action)
    {
        $method = $action.'Action';
        return $method;
    }
}