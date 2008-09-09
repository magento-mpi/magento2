<?php
abstract class WebService_TestCase_Abstract extends PHPUnit_Framework_TestCase
{
    protected static $_soapConnector;
    protected static $_rpcConnector;

    protected function setUp()
    {
        self::$_soapConnector->init(WebService_Connector_Configuration::getSoapApiUrl());
        self::$_rpcConnector->init(WebService_Connector_Configuration::getRpcApiUrl());

        self::$_soapConnector->startSession(
            WebService_Connector_Configuration::getApiLogin(),
            WebService_Connector_Configuration::getApiPassword()
        );

        self::$_rpcConnector->startSession(
            WebService_Connector_Configuration::getApiLogin(),
            WebService_Connector_Configuration::getApiPassword()
        );
    }

    protected function tearDown()
    {
        self::$_soapConnector->endSession();
        self::$_rpcConnector->endSession();
    }

    public static function connectorProvider()
    {
        self::$_soapConnector = new WebService_Connector_Soap();
        self::$_rpcConnector = new WebService_Connector_Rpc();

        return
            array(
                array(self::$_rpcConnector),
                array(self::$_soapConnector)
            );
    }
}