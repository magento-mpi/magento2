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
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Mage
 * @package    Mage_Connect
 * @copyright  Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
* Class Controller
*
* @category   Mage
* @package    Mage_Connect
* @copyright  Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
* @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*/

final class Maged_Controller
{

    /**
    * Key of action
    */
    const ACTION_KEY = 'A';

    /**
    * Instance of class
    *
    * @var Maged_Controller
    */
    private static $_instance;

    private $_action;
    private $_isDispatched = false;
    private $_redirectUrl;
    private $_rootDir;

    private $_view;
    private $_config;
    private $_session;

    private $_writable;

    private $_useCache;

    //////////////////////////// ACTIONS

    /**
    * NoRoute
    */
    public function norouteAction()
    {
        header("HTTP/1.0 404 Invalid Action");
        echo $this->view()->template('noroute.phtml');
    }

    /**
    * Login
    */
    public function loginAction()
    {
        $this->view()->set('username', !empty($_GET['username']) ? $_GET['username'] : '');
        echo $this->view()->template('login.phtml');
    }

    /**
    * Logout
    */
    public function logoutAction()
    {
        $this->session()->logout();
        $this->redirect($this->url());
    }

    /**
    * Index
    */
    public function indexAction()
    {
        if (!$this->isInstalled()) {
            if (!$this->isWritable()) {
                echo $this->view()->template('install/writable.phtml');
            } else {
                $this->view()->set('mage_url', dirname(dirname($_SERVER['SCRIPT_NAME'])));
                $this->view()->set('use_custom_permissions_mode', $this->config()->get('use_custom_permissions_mode'));
                $this->view()->set('mkdir_mode', $this->config()->get('mkdir_mode'));
                $this->view()->set('chmod_file_mode', $this->config()->get('chmod_file_mode'));
                $this->view()->set('protocol', $this->config()->get('protocol'));
                
                echo $this->view()->template('install/download.phtml');
            }
        } else {
            if (!$this->isWritable()) {
                echo $this->view()->template('writable.phtml');
            } else {
                $this->forward('connectPackages');
            }
        }
    }

    /**
    * Empty Action
        */
    public function emptyAction()
    {
        $this->model('connect', true)->connect()->runHtmlConsole('Please wait, preparing for updates...');
    }

    /**
    * Install all magento
    */
    public function connectInstallAllAction()
    {
        $this->config()->saveConfigPost($_POST);
        $chan = $this->config()->get('root_channel');
        if(empty($chan)) {
            $chan = 'core';
        }
        $this->model('connect', true)->saveConfigPost($_POST);
        $this->model('connect', true)->installAll(!empty($_GET['force']), $chan);
    }

    /**
    * Connect packages
    */
    public function connectPackagesAction()
    {
        $connect = $this->model('connect', true);
        $this->view()->set('connect', $connect);
        echo $this->view()->template('connect/packages.phtml');
    }

    /**
    * Connect packages POST
    */
    public function connectPackagesPostAction()
    {
        $actions = isset($_POST['actions']) ? $_POST['actions'] : array();
        $ignoreLocalModification = isset($_POST['ignore_local_modification'])?$_POST['ignore_local_modification']:'';
        $this->model('connect', true)->applyPackagesActions($actions, $ignoreLocalModification);
    }

    /**
    * Install package
    */
    public function connectInstallPackagePostAction()
    {
        if (!$_POST) {
            echo "INVALID POST DATA";
            return;
        }
        $this->model('connect', true)->installPackage($_POST['install_package_id']);
    }

    /**
    * Settings
    */
    public function settingsAction()
    {
        $connectConfig = $this->model('connect', true)->connect()->getConfig();
        $this->view()->set('preferred_state', $connectConfig->__get('preferred_state'));
        $this->view()->set('protocol', $connectConfig->__get('protocol'));
        $this->view()->set('use_custom_permissions_mode', $this->config()->get('use_custom_permissions_mode'));
        $this->view()->set('mkdir_mode', $this->config()->get('mkdir_mode'));
        $this->view()->set('chmod_file_mode', $this->config()->get('chmod_file_mode'));

        echo $this->view()->template('settings.phtml');
    }

    /**
    * Settings post
    */
    public function settingsPostAction()
    {
        if ($_POST) {
            $this->config()->saveConfigPost($_POST);
            $this->model('connect', true)->saveConfigPost($_POST);
        }
        $this->redirect($this->url('settings'));
    }

    //////////////////////////// ABSTRACT

    /**
    * Constructor
    */
    public function __construct()
    {
        $this->_rootDir = dirname(dirname(__FILE__));
        $this->_mageDir = dirname($this->_rootDir);
    }

    /**
    * Run
    */
    public static function run()
    {
        try {
            self::singleton()->dispatch();
        } catch (Exception $e) {
            echo $e->getMessage();
            //echo self::singleton()->view()->set('exception', $e)->template("exception.phtml");
        }
    }

    /**
    * Initialize object of class
    *
    * @return
    */
    public static function singleton()
    {
        if (!self::$_instance) {
            self::$_instance = new self;

            if (self::$_instance->isDownloaded() && self::$_instance->isInstalled()) {
                Mage::app();
                Mage::getSingleton('adminhtml/url')->turnOffSecretKey();
            }
        }
        return self::$_instance;
    }

    public function getRootDir()
    {
        return $this->_rootDir;
    }

    public function getMageDir()
    {
        return $this->_mageDir;
    }

    public function getMageFilename()
    {
        $ds = DIRECTORY_SEPARATOR;
        return $this->getMageDir().$ds.'app'.$ds.'Mage.php';
    }

    public function getVarFilename()
    {
        $ds = DIRECTORY_SEPARATOR;
        return $this->getMageDir().$ds.'lib'.$ds.'Varien'.$ds.'Profiler.php';
    }

    public function filepath($name='')
    {
        $ds = DIRECTORY_SEPARATOR;
        return rtrim($this->getRootDir().$ds.str_replace('/', $ds, $name), $ds);
    }

    /**
    * Retrieve object of view
    *
    * @return Maged_View
    */
    public function view()
    {
        if (!$this->_view) {
            $this->_view = new Maged_View;
        }
        return $this->_view;
    }

    /**
    * Retrieve object of model
    *
    * @param string $model
    * @param boolean $singleton
    * @return Maged_Model
    */
    public function model($model=null, $singleton=false)
    {
        if ($singleton && isset($this->_singletons[$model])) {
            return $this->_singletons[$model];
        }

        if (is_null($model)) {
            $class = 'Maged_Model';
        } else {
            $class = 'Maged_Model_'.str_replace(' ', '_', ucwords(str_replace('_', ' ', $model)));
            if (!class_exists($class, false)) {
                include_once str_replace('_', DIRECTORY_SEPARATOR, $class).'.php';
            }
        }

        $object = new $class();

        if ($singleton) {
            $this->_singletons[$model] = $object;
        }

        return $object;
    }

    /**
    * Retrieve object of config
    *
    * @return Maged_Model_Config
    */
    public function config()
    {
        if (!$this->_config) {
            $this->_config = $this->model('config')->load();
        }
        return $this->_config;
    }

    /**
    * Retrieve object of session
    *
    * @return Maged_Model_Session
    */
    public function session()
    {
        if (!$this->_session) {
            $this->_session = $this->model('session')->start();
        }
        return $this->_session;
    }

    public function setAction($action=null)
    {
        if (is_null($action)) {
            if (!empty($this->_action)) {
                return $this;
            }
            $action = !empty($_GET[self::ACTION_KEY]) ? $_GET[self::ACTION_KEY] : 'index';
        }
        if (empty($action) || !is_string($action)
            || !method_exists($this, $this->getActionMethod($action))) {
            $action = 'noroute';
        }
        $this->_action = $action;
        return $this;
    }

    public function getAction()
    {
        return $this->_action;
    }

    public function redirect($url, $force=false)
    {
        $this->_redirectUrl = $url;
        if ($force) {
            $this->processRedirect();
        }
        return $this;
    }

    /**
    * Precess redirect
    *
    * @return Maged_Controller
    */
    public function processRedirect()
    {
        if ($this->_redirectUrl) {
            if (headers_sent()) {
                echo '<script type="text/javascript">location.href="'.$this->_redirectUrl.'"</script>';
                exit;
            } else {
                header("Location: ".$this->_redirectUrl);
                exit;
            }
        }
        return $this;
    }

    /**
    * Forward
    *
    * @param string $action
    * @return Maged_Controller
    */
    public function forward($action)
    {
        $this->setAction($action);
        $this->_isDispatched = false;
        return $this;
    }

    public function getActionMethod($action = null)
    {
        $method = (!is_null($action) ? $action : $this->_action).'Action';
        return $method;
    }

    public function url($action='', $params=array())
    {
        $paramsStr = '';
        foreach ($params as $k=>$v) {
            $paramStr .= '&'.$k.'='.urlencode($v);
        }
        return $_SERVER['SCRIPT_NAME'].'?'.self::ACTION_KEY.'='.$action.$paramsStr;
    }

    public function dispatch()
    {
        header('Content-type: text/html; charset=UTF-8');

        $this->setAction();

        if (!$this->isWritable() || !$this->isInstalled()) {
            if (!in_array($this->getAction(), array('index', 'connectInstallAll', 'empty'))) {
                $this->setAction('index');
            }
        } else {
            $this->session()->authenticate();
        }

        while (!$this->_isDispatched) {
            $this->_isDispatched = true;

            $method = $this->getActionMethod();
            $this->$method();
        }

        $this->processRedirect();
    }

    public function isWritable()
    {
        if (is_null($this->_writable)) {
            $this->_writable = is_writable($this->getMageDir() . DIRECTORY_SEPARATOR)
                && is_writable($this->filepath())
                && (!file_exists($this->filepath('config.ini') || is_writable($this->filepath('config.ini'))));

        }
        return $this->_writable;
    }

    public function isDownloaded()
    {
        return file_exists($this->getMageFilename())
            && file_exists($this->getVarFilename());
    }

    public function isInstalled()
    {
        if (!$this->isDownloaded()) {
            return false;
        }
        if (!class_exists('Mage', false)) {
            if(!file_exists($this->getMageFilename())) {
                return false;                
            }
            include_once $this->getMageFilename();
            Mage::setIsDownloader();
        } 
        return Mage::isInstalled();
    }

    /**
    * Begin install package
    */
    public function startInstall()
    {
		
    }

    /**
    * End install package
    */
    public function endInstall()
    {
        try {
            if (!empty($_GET['clean_sessions'])) {
                Mage::app()->cleanAllSessions();
            }
            Mage::app()->cleanCache();
        } catch (Exception $e) {
            $this->session()->addMessage('error', "Exception during cache and session cleaning: ".$e->getMessage());
        }
    }
    
    
    public function connectInstallPackageUploadAction()
    {
    	if (!$_FILES) {
            echo "No file was uploaded";
            return;
        }
        
        if(empty($_FILES['file'])) {
        	echo "No file was uploaded";
        	return;
        }
        
        $info =& $_FILES['file'];
        
        if(0 !== intval($info['error'])) {
        	echo "File upload problem";
        	return;
        }
        
        $target = "var/".uniqid().$info['name'];
        $res = move_uploaded_file($info['tmp_name'], $target);
        if(false === $res) {
        	echo "Error moving uploaded file";
        	return;	
        }
        
        $this->model('connect', true)->installUploadedPackage($target);
        @unlink($target);
	}
}
