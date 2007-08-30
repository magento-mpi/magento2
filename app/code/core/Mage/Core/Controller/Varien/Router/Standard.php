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

class Mage_Core_Controller_Varien_Router_Standard extends Mage_Core_Controller_Varien_Router_Abstract
{
    protected $_modules = array();
    protected $_dispatchData = array();
    
    public function collectRoutes($configArea, $useRouterName)
    {
        $routers = array();
        $routersConfigNode = Mage::getConfig()->getNode($configArea.'/routers');
        if($routersConfigNode) {
        	$routers = $routersConfigNode->children();
        }        
        foreach ($routers as $routerName=>$routerConfig) {
            $use = (string)$routerConfig->use;
            if ($use==$useRouterName) {
                $module = (string)$routerConfig->args->module;
                $frontName = (string)$routerConfig->args->frontName;
                $this->addModule($frontName, $module);
            }
        }
    }
    
    public function fetchDefault()
    {
        $storeCode = Mage::registry('controller')->getStoreCode();
        if(Mage::getConfig()->getIsInstalled()) {
        	$store = Mage::getSingleton('core/store')->load($storeCode);
        	Mage::getSingleton('core/website')->load($store->getWebsiteId());
        } else {
        	$store = Mage::getSingleton('core/store')->setId(0)->setCode($storeCode);
        }
        
    	// set defaults
        $d = explode('/', Mage::getStoreConfig('web/default/front'));
        $this->getFront()->setDefault(array(
            'module'     => !empty($d[0]) ? $d[0] : 'core', 
            'controller' => !empty($d[1]) ? $d[1] : 'index', 
            'action'     => !empty($d[2]) ? $d[2] : 'index'
        ));
    }
    
    public function match(Zend_Controller_Request_Http $request)
    {
        $this->fetchDefault();

        $front = $this->getFront();
        
        $p = explode('/', trim($request->getPathInfo(), '/'));

        // get module name
        if ($request->getModuleName()) {
            $module = $request->getModuleName();
        } else {
        	$p = explode('/', trim($request->getPathInfo(), '/'));
            $module = !empty($p[0]) ? $p[0] : $this->getFront()->getDefault('module');
        }
		if (!$module) {
			return false;
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

        return true;#$request->isDispatched();
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
                    $paramsGetPost = array();
                    if ($post = $this->getFront()->getRequest()->getPost()) {
                        $paramsGetPost+= $post;
                    }
                    if ($get = $this->getFront()->getRequest()->getQuery()) {
                        $paramsGetPost+= $get;
                    }
                    
                    $params = array_merge($this->getFront()->getRequest()->getParams(), $params);
                    $params = array_diff_key($params, $paramsGetPost);
                } elseif (is_array($params['_current'])) {
                    foreach ($params['_current'] as $param) {
                        $params[$param] = $this->getFront()->getRequest()->getParam($param);
                    }
                }
            }
            $paramsStr = '';
            foreach ($params as $key=>$value) {
                if (!empty($key) && !isset($reservedKeys[$key]) && '_'!==$key{0} && !empty($value)) {
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
            
            // adding query params to current option
            $query = http_build_query($this->getFront()->getRequest()->getQuery());
            if (!empty($query) && isset($params['_current']) && $params['_current']===true) {
                $url .= '?' . $query;
            }
        }

        return $url;
    }
    
    public function rewrite(array $p) 
    {
    	$rewrite = Mage::getConfig()->getNode('global/rewrite');
        if ($module = $rewrite->{$p[0]}) {
        	if (!$module->children()) {
        		$p[0] = trim((string)$module);
        	}
        }
        if (isset($p[1]) && ($controller = $rewrite->{$p[0]}->{$p[1]})) {
        	if (!$controller->children()) {
        		$p[1] = trim((string)$controller);
        	}
        }
        if (isset($p[2]) && ($action = $rewrite->{$p[0]}->{$p[1]}->{$p[2]})) {
        	if (!$action->children()) {
        		$p[2] = trim((string)$action);
        	}
        }
#echo "<pre>".print_r($p,1)."</pre>";
        return $p;
    }
}