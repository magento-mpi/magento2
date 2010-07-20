<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Provider
 *
 * @author Vladimir
 */
class WebService_Connector_Provider
{
    protected static $_connector = null;
    protected static $_soapV1Connector = null;
    protected static $_soapV2Connector = null;
    protected static $_rpcConnector = null;

    public static function connect ($type='XmlRpc')
    {
        switch($type) {
        case 'SoapV1':
            if (is_null(self::$_soapV1Connector)) {
                self::$_soapV1Connector = new WebService_Connector_Soap();
                self::$_soapV1Connector->init(WebService_Connector_Configuration::getSoapApiUrl());
                self::$_soapV1Connector->startSession(
                    WebService_Connector_Configuration::getApiLogin(),
                    WebService_Connector_Configuration::getApiPassword()
                );
            }
            self::$_connector = self::$_soapV1Connector;
            break;
        case 'SoapV2':
            if (is_null(self::$_soapV2Connector)) {
                self::$_soapV2Connector = new WebService_Connector_SoapV2();
                self::$_soapV2Connector->init(WebService_Connector_Configuration::getSoapV2ApiUrl());
                self::$_soapV2Connector->startSession(
                    WebService_Connector_Configuration::getApiLogin(),
                    WebService_Connector_Configuration::getApiPassword()
                );
            }
            self::$_connector = self::$_soapV2Connector;
            break;
        case 'XmlRpc':
            if (is_null(self::$_rpcConnector)) {
                self::$_rpcConnector = new WebService_Connector_Rpc();
                self::$_rpcConnector->init(WebService_Connector_Configuration::getRpcApiUrl());
                self::$_rpcConnector->startSession(
                    WebService_Connector_Configuration::getApiLogin(),
                    WebService_Connector_Configuration::getApiPassword()
                );
            }
            self::$_connector =  self::$_rpcConnector;
            break;
        }

        return self::$_connector;
    }

    public static function disconnect($type='all')
    {
        switch($type) {
        case 'SoapV1':
            if (!is_null(self::$_soapV1Connector)) {
                self::$_soapV1Connector->endSession();
            }
            self::$_soapV1Connector = null;
            break;
        case 'SoapV2':
            if (!is_null(self::$_soapV2Connector)) {
                self::$_soapV2Connector->endSession();
            }
            self::$_soapV2Connector = null;
            break;
        case 'XmlRpc':
            if (!is_null(self::$_rpcConnector)) {
                self::$_rpcConnector->endSession();
            }
            self::$_rpcConnector = null;
            break;
        default:
            self::disconnect('SoapV1');
            self::disconnect('SoapV2');
            self::disconnect('XmlRpc');
            self::$_connector = null;
            break;
        }
    }

    function __destruct()
    {
        if (!is_null(self::$_soapV1Connector)) {
            self::$_soapV1Connector->endSession();
        }
        if (!is_null(self::$_soapV2Connector)) {
            self::$_soapV2Connector->endSession();
        }

        if (!is_null(self::$_rpcConnector)) {
            self::$_rpcConnector->endSession();
        }
        
        self::$_connector = null;
    }
}
?>
