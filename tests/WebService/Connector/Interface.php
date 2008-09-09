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


interface WebService_Connector_Interface
{
    /**
     * Create connection to specified URL
     *
     * @param string $url
     * @return WebService_Connector_Interface
     */
    public function init($url);

    /**
     * Start session on current connection
     *
     * @param string $apiLogin
     * @param string $apiPassword
     * @return WebService_Connector_Interface
     */
    public function startSession($apiLogin, $apiPassword);

    /**
     * Stop session on current connection
     *
     * @return WebService_Connector_Interface
     */
    public function endSession();

    /**
     * Call specified method with specified params on current connection
     *
     * @param array $method
     * @param mixed $params
     * @return mixed
     */
    public function call($method, $params = array());

    /**
     * Multicall specified methods on current connection
     *
     * @param array $methods
     * @param mixed $options
     * @return mixed
     */
    public function multiCall($methods, $options = null);

    /**
     * Return list of available API resources and methods allowed for current session
     *
     * @return array
     */
    public function listResources();

    /**
     * Return list of fault messages and their codes, that do not depend on any resource
     *
     * @return array
     */
    public function getGlobalFaults();

    /**
     * Return list of the resource fault messages, if this resource is allowed in current session
     *
     * @return array
     */
    public function getResourceFaults();
}