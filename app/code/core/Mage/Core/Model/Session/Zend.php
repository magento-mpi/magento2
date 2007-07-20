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
abstract class Mage_Core_Model_Session_Zend extends Varien_Object
{
    /**
     * Session namespace object
     *
     * @var Zend_Session_Namespace
     */
    protected $_session;
    
    /**
     * Initialization session namespace
     *
     * @param string $namespace
     */
    public function init($namespace)
    {
        Varien_Profiler::start(__METHOD__.'/init');
        $this->_session = new Zend_Session_Namespace($namespace, Zend_Session_Namespace::SINGLE_INSTANCE);
        Varien_Profiler::stop(__METHOD__.'/init');
        return $this;
    }
    
    public function start()
    {
        Varien_Profiler::start(__METHOD__.'/setOptions');
        Zend_Session::setOptions(array('save_path'=>Mage::getBaseDir('session'), 'use_only_cookies'=>'off'));
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
     * Redeclaration object setter
     *
     * @param   string $key
     * @param   mixed $value
     * @return  Mage_Core_Model_Session_Abstract
     */
    public function setData($key, $value='', $isChanged = false)
    {
        if (!$this->_session->data) {
            $this->_session->data = new Varien_Object();
        }
        $this->_session->data->setData($key, $value, $isChanged);
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
        if (!$this->_session->data) {
            $this->_session->data = new Varien_Object();
        }

        $data = $this->_session->data->getData($var);
        
        if ($clear) {
            $this->_session->data->unsetData($var);
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
        $this->_session->unsetAll();
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
        if (!$this->_session->messages) {
            $this->_session->messages = Mage::getModel('core/message_collection');
        }
        
        if ($clear) {
            $messages = clone $this->_session->messages;
            $this->_session->messages->clear();
            return $messages;
        }
        return $this->_session->messages;
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
}