<?php
/**
 * Core session
 *
 * @package    Ecom
 * @subpackage Core
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Core_Session
{
    protected $_namespaces = array();
    public function __construct() 
    {
        Zend_Session::setOptions(array('save_path'=>Mage::getBaseDir('var').DS.'session'));
        Zend_Session::start();
    }
    
    /**
     * Get session namespace
     *
     * @param   string $namespaceName
     * @return  Zend_Session_Namespace
     */
    public function getNamespace($namespaceName)
    {
        if (!isset($this->_namespaces[$namespaceName])) {
            $this->_namespaces[$namespaceName] = new Zend_Session_Namespace($namespaceName);
            
            if (empty($this->_namespaces[$namespaceName]->_data)) {
                $this->_namespaces[$namespaceName]->_data = new Varien_Data_Object();
            }
            
            if (empty($this->_namespaces[$namespaceName]->_message)) {
                $this->_namespaces[$namespaceName]->_message = new Mage_Core_Message();
            }            
        }
        return $this->_namespaces[$namespaceName];
    }
    
    /**
     * Get data from session namespace
     *
     * @param   string $namespaceName
     * @param   bool $clear
     * @return  Varian_DataObject
     */
    public function getNamespaceData($namespaceName, $clear = true)
    {
        $data = $this->getNamespace($namespaceName)->_data;
        if ($clear) {
            $this->getNamespace($namespaceName)->_data = new Varien_Data_Object();
        }
        return $data;
    }
    
    /**
     * Get message object from session namespace
     *
     * @param   string $namespaceName
     * @param   bool $clear
     * @return  Mage_Core_Message
     */
    public function getNamespaceMessage($namespaceName, $clear = true)
    {
        $message = $this->getNamespace($namespaceName)->_message;
        if ($clear) {
            $this->getNamespace($namespaceName)->_message = new Mage_Core_Message();
        }
        return $message;
    }
}