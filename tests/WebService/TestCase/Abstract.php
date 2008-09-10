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


abstract class WebService_TestCase_Abstract extends PHPUnit_Framework_TestCase
{
    protected static $_soapConnector = null;
    protected static $_rpcConnector = null;

    public static function connectorProvider()
    {
        if (is_null(self::$_soapConnector)) {
            self::$_soapConnector = new WebService_Connector_Soap();
            self::$_soapConnector->init(WebService_Connector_Configuration::getSoapApiUrl());
            self::$_soapConnector->startSession(
                WebService_Connector_Configuration::getApiLogin(),
                WebService_Connector_Configuration::getApiPassword()
            );
        }

        if (is_null(self::$_rpcConnector)) {
            self::$_rpcConnector = new WebService_Connector_Rpc();
            self::$_rpcConnector->init(WebService_Connector_Configuration::getRpcApiUrl());
            self::$_rpcConnector->startSession(
                WebService_Connector_Configuration::getApiLogin(),
                WebService_Connector_Configuration::getApiPassword()
            );
        }

        return
            array(
                array(self::$_rpcConnector),
                array(self::$_soapConnector)
            );
    }

    function __destruct()
    {
        if (!is_null(self::$_soapConnector)) {
            self::$_soapConnector->endSession();
        }

        if (!is_null(self::$_rpcConnector)) {
            self::$_rpcConnector->endSession();
        }
    }
}