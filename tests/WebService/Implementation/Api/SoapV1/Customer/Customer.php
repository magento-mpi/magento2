<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Customer
 *
 * @author Anton
 */
class WebService_Implementation_Api_SoapV1_Customer_Customer
{
    public static function customerCreate($xmlPath)
    {
        $xml = simplexml_load_file($xmlPath);
        $data = WebService_Helper_Xml::simpleXMLToArray($xml);
        return WebService_Connector_Provider::connect('SoapV1')
            ->getConnection()
            ->call(
                WebService_Connector_Provider::connect('SoapV1')->getSession(),
                'customer.create',
                array($data['CustomerEntityToCreate'])
        );
    }

    public static function customerList($xmlPath)
    {
        $xml = simplexml_load_file($xmlPath);
        $data = WebService_Helper_Xml::simpleXMLToArray($xml);
        $result =  WebService_Connector_Provider::connect('SoapV1')
            ->getConnection()
            ->call(
                WebService_Connector_Provider::connect('SoapV1')->getSession(),
                'customer.list',
                array($data['filters'])
        );
        return WebService_Helper_Xml::applyIgnore($result, $data['ignore']);
    }

    public static function customerInfo($xmlPath)
    {
        $xml = simplexml_load_file($xmlPath);
        $data = WebService_Helper_Xml::simpleXMLToArray($xml);
        $result = WebService_Connector_Provider::connect('SoapV1')
            ->getConnection()
            ->call(
                WebService_Connector_Provider::connect('SoapV1')->getSession(),
                'customer.info',
                array($data['parameters'])
        );
        return WebService_Helper_Xml::applyIgnore($result, $data['ignore']);
    }

    public static function customerUpdate($xmlPath)
    {
        $xml = simplexml_load_file($xmlPath);
        $data = WebService_Helper_Xml::simpleXMLToArray($xml);
        return  WebService_Connector_Provider::connect('SoapV1')
            ->getConnection()
            ->call(
                WebService_Connector_Provider::connect('SoapV1')->getSession(),
                'customer.update',
                array($data['parameters'], $data['CustomerEntityToUpdate'])
        );
    }

    public static function customerDelete($xmlPath)
    {
        $xml = simplexml_load_file($xmlPath);
        $data = WebService_Helper_Xml::simpleXMLToArray($xml);
        return  WebService_Connector_Provider::connect('SoapV1')
            ->getConnection()
            ->call(
                 WebService_Connector_Provider::connect('SoapV1')->getSession(),
                'customer.delete',
                array($data['parameters'])
        );
    }
}