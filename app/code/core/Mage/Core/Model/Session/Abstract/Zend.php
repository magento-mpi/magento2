<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Session abstaract class
 *
 * @category   Mage
 * @package    Mage_Core
 * @author      Magento Core Team <core@magentocommerce.com>
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
        Magento_Profiler::start(__METHOD__.'/setOptions');
        $options = array(
            'save_path'=>Mage::getBaseDir('session'),
            'use_only_cookies'=>'off',
            'throw_startup_exceptions' => E_ALL ^ E_NOTICE,
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
        Magento_Profiler::stop(__METHOD__.'/setOptions');
/*
        Magento_Profiler::start(__METHOD__.'/setHandler');
        $sessionResource = Mage::getResourceSingleton('Mage_Core_Model_Resource_Session');
        if ($sessionResource->hasConnection()) {
            Zend_Session::setSaveHandler($sessionResource);
        }
        Magento_Profiler::stop(__METHOD__.'/setHandler');
*/
        Magento_Profiler::start(__METHOD__.'/start');
        Zend_Session::start();
        Magento_Profiler::stop(__METHOD__.'/start');

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

        Magento_Profiler::start(__METHOD__.'/init');
        $this->_namespace = new Zend_Session_Namespace($namespace, Zend_Session_Namespace::SINGLE_INSTANCE);
        Magento_Profiler::stop(__METHOD__.'/init');
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
     * Retrieve current session identifier
     *
     * @return string
     */
    public function getSessionId()
    {
        return Zend_Session::getId();
    }

    public function setSessionId($id=null)
    {
        if (!is_null($id)) {
            Zend_Session::setId($id);
        }
        return $this;
    }

    /**
     * Regenerate session Id
     *
     * @return Mage_Core_Model_Session_Abstract_Zend
     */
    public function regenerateSessionId()
    {
        Zend_Session::regenerateId();
        return $this;
    }
}
