<?php
/**
 * Session abstaract class
 *
 * @package     Mage
 * @subpackage  Core
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
abstract class Mage_Core_Model_Session_Abstract_Zend extends Varien_Object
{
    /**
     * Session namespace object
     *
     * @var Zend_Session_Namespace
     */
    protected $_namespace;
    
    public function getNamespace()
    {
    	return $this->_namespace;
    }
    
    public function start()
    {
        Varien_Profiler::start(__METHOD__.'/setOptions');
        $options = array(
        	'save_path'=>Mage::getBaseDir('session'), 
        	'use_only_cookies'=>'off',
        );
        if ($this->getCookieDomain()) {
        	$options['cookie_domain'] = $this->getCookieDomain();
        }
        if ($this->getCookiePath()) {
        	$options['cookie_path'] = $this->getCookiePath();
        }
        if ($this->getCookieLifetime()) {
        	$options['cookie_lifetime'] = $this->getCookieLifetime();
        }
        Zend_Session::setOptions($options);
        Varien_Profiler::stop(__METHOD__.'/setOptions');
/*
        Varien_Profiler::start(__METHOD__.'/setHandler');
        $sessionResource = Mage::getResourceSingleton('core/session');
        if ($sessionResource->hasConnection()) {
        	Zend_Session::setSaveHandler($sessionResource);
        }
        Varien_Profiler::stop(__METHOD__.'/setHandler');
*/     
        Varien_Profiler::start(__METHOD__.'/start');
        Zend_Session::start();
        Varien_Profiler::stop(__METHOD__.'/start');
        
        return $this;
    }
    
    /**
     * Initialization session namespace
     *
     * @param string $namespace
     */
    public function init($namespace)
    {
    	if (!Zend_Session::sessionExists()) {
    		$this->start();
    	}
    	
        Varien_Profiler::start(__METHOD__.'/init');
        $this->_namespace = new Zend_Session_Namespace($namespace, Zend_Session_Namespace::SINGLE_INSTANCE);
        Varien_Profiler::stop(__METHOD__.'/init');
        return $this;
    }
    
    /**
     * Redeclaration object setter
     *
     * @param   string $key
     * @param   mixed $value
     * @return  Mage_Core_Model_Session_Abstract
     */
    public function setData($key, $value='', $isChanged = false)
    {
        if (!$this->_namespace->data) {
            $this->_namespace->data = new Varien_Object();
        }
        $this->_namespace->data->setData($key, $value, $isChanged);
        return $this;
    }
    
    /**
     * Redeclaration object getter
     *
     * @param   string $var
     * @param   bool $clear
     * @return  mixed
     */
    public function getData($var=null, $clear=false)
    {
        if (!$this->_namespace->data) {
            $this->_namespace->data = new Varien_Object();
        }

        $data = $this->_namespace->data->getData($var);
        
        if ($clear) {
            $this->_namespace->data->unsetData($var);
        }

        return $data;
    }
    
    /**
     * Cleare session data
     *
     * @return Mage_Core_Model_Session_Abstract
     */
    public function unsetAll()
    {
        $this->_namespace->unsetAll();
        return $this;
    }
    
    /**
     * Retrieve messages from session
     *
     * @param   bool $clear
     * @return  Mage_Core_Model_Message_Collection
     */
    public function getMessages($clear=false)
    {
        if (!$this->_namespace->messages) {
            $this->_namespace->messages = Mage::getModel('core/message_collection');
        }
        
        if ($clear) {
            $messages = clone $this->_namespace->messages;
            $this->_namespace->messages->clear();
            return $messages;
        }
        return $this->_namespace->messages;
    }
    
    /**
     * Adding new message to message collection
     *
     * @param   Mage_Core_Model_Message_Abstract $message
     * @return  Mage_Core_Model_Session_Abstract
     */
    public function addMessage(Mage_Core_Model_Message_Abstract $message)
    {
        $this->getMessages()->add($message);
        return $this;
    }
    
    /**
     * Adding new error message
     *
     * @param   string $message
     * @return  Mage_Core_Model_Session_Abstract
     */
    public function addError($message)
    {
        $this->addMessage(Mage::getSingleton('core/message')->error($message));
        return $this;
    }
    
    /**
     * Adding new warning message
     *
     * @param   string $message
     * @return  Mage_Core_Model_Session_Abstract
     */
    public function addWarning($message)
    {
        $this->addMessage(Mage::getSingleton('core/message')->warning($message));
        return $this;
    }
    
    /**
     * Adding new nitice message
     *
     * @param   string $message
     * @return  Mage_Core_Model_Session_Abstract
     */
    public function addNotice($message)
    {
        $this->addMessage(Mage::getSingleton('core/message')->notice($message));
        return $this;
    }
    
    /**
     * Adding new success message
     *
     * @param   string $message
     * @return  Mage_Core_Model_Session_Abstract
     */
    public function addSuccess($message)
    {
        $this->addMessage(Mage::getSingleton('core/message')->success($message));
        return $this;
    }
    
    /**
     * Adding messages array to message collection
     *
     * @param   array $messages
     * @return  Mage_Core_Model_Session_Abstract
     */
    public function addMessages($messages)
    {
        if (is_array($messages)) {
            foreach ($messages as $message) {
                $this->addMessage($message);
            }
        }
        return $this;
    }
    
    /**
     * Retrieve current session identifier
     *
     * @return string
     */
    public function getSessionId()
    {
        return Zend_Session::getId();
    }
    
    public function getCookieDomain()
    {
    	$domain = Mage::getStoreConfig('web/cookie/cookie_domain');
    	if (empty($domain)) {
    		$domainArr = explode(':', $_SERVER['HTTP_HOST']);
    		$domain = $domainArr[0];
    	}
    	return $domain;
    }

    public function getCookiePath()
    {
    	$path = Mage::getStoreConfig('web/cookie/cookie_path');
    	if (empty($path)) {
    		$path = '/';
    	}
    	return $path;
    }
    
    public function getCookieLifetime()
    {
    	$lifetime = Mage::getStoreConfig('web/cookie/cookie_lifetime');
    	if (empty($lifetime)) {
    		$lifetime = 60*60*3;
    	}
    	return $lifetime;
    }

}