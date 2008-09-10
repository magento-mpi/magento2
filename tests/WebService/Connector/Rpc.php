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
 * @package    Mage_Tests
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


class WebService_Connector_Rpc implements WebService_Connector_Interface
{
    /**
     * Create XmlRpc connection to specified URL
     *
     * @param string $url
     * @return WebService_Connector_Interface
     */
    public function init($url){
        $this->_connection = new Zend_XmlRpc_Client($url);
        return $this;
    }

    /**
     * Start session on current XmlRpc connection
     *
     * @param string $apiLogin
     * @param string $apiPassword
     * @return WebService_Connector_Interface
     */
    public function startSession($apiLogin, $apiPassword){
        $this->_session = $this->_connection->call('login', array($apiLogin, $apiPassword));
        return $this;
    }

    /**
     * Stop session on current XmlRpc connection
     *
     * @return WebService_Connector_Interface
     */
    public function endSession(){
        $this->_connection->call('endSession', array($this->_session));
        return $this;
    }

    /**
     * Call specified method with specified params on current XmlRpc connection
     *
     * @param array $method
     * @param mixed $params
     * @return mixed
     */
    public function call($method, $params = null){
        $callParams = array($this->_session, $method);
        if (func_num_args() > 1) {
            if (!is_array($params)) {
                $params = array($params);
            }

            $callParams[] = $params;
        }

        return $this->_connection->call('call', $callParams);
    }

    /**
     * Multicall specified methods on current XmlRpc connection
     *
     * @param array $methods
     * @param mixed $options
     * @return mixed
     */
    public function multiCall($methods, $options = null){
        return $this->_connection->call('multiCall', array($this->_session, $methods, $options));
    }

    /**
     * Return list of available API resources and methods allowed for current session
     *
     * @return array
     */
    public function listResources(){
        return $this->_connection->call('resources', array($this->_session));
    }

    /**
     * Return list of fault messages and their codes, that do not depend on any resource
     *
     * @return array
     */
    public function getGlobalFaults(){
        return $this->_connection->call('globalFaults', array($this->_session));
    }

    /**
     * Return list of the resource fault messages, if this resource is allowed in current session
     *
     * @return array
     */
    public function getResourceFaults(){
        return $this->_connection->call('resourceFaults', array($this->_session));
    }
}