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


class WebService_Connector_Soap implements WebService_Connector_Interface
{
    /**
     * Create SOAP connection to specified URL
     *
     * @param string $url
     * @return WebService_Connector_Interface
     */
    public function init($url){
        $this->_connection = new SoapClient($url);
        return $this;
    }

    /**
     * Start session on current SOAP connection
     *
     * @param string $apiLogin
     * @param string $apiPassword
     * @return WebService_Connector_Interface
     */
    public function startSession($apiLogin, $apiPassword){
        $this->_session = $this->_connection->login($apiLogin, $apiPassword);
        return $this;
    }

    /**
     * Stop session on current SOAP connection
     *
     * @return WebService_Connector_Interface
     */
    public function endSession(){
        $this->_connection->endSession($this->_session);
        return $this;
    }

    /**
     * Call specified method with specified params on current SOAP connection
     *
     * @param array $method
     * @param mixed $params
     * @return mixed
     */
    public function call($method, $params = array()){
        return $this->_connection->call($this->_session, $method, $params);
    }

    /**
     * Multicall specified methods on current SOAP connection
     *
     * @param array $methods
     * @param mixed $options
     * @return mixed
     */
    public function multiCall($methods, $options = null){
        return $this->_connection->multiCall($this->_session, $methods, $options);
    }

    /**
     * Return list of available API resources and methods allowed for current session
     *
     * @return array
     */
    public function listResources(){
        return $this->_connection->resources($this->_session);
    }

    /**
     * Return list of fault messages and their codes, that do not depend on any resource
     *
     * @return array
     */
    public function getGlobalFaults(){
        return $this->_connection->globalFaults($this->_session);
    }

    /**
     * Return list of the resource fault messages, if this resource is allowed in current session
     *
     * @return array
     */
    public function getResourceFaults(){
        return $this->_connection->resourceFaults($this->_session);
    }
}