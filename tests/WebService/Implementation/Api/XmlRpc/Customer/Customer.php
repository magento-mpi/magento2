<?php

class WebService_Implementation_Api_XmlRpc_Customer_Customer
{
    public static function customerCreate($xmlPath)
    {
        $xml = simplexml_load_file($xmlPath);
        $data = WebService_Helper_Xml::simpleXMLToArray($xml);

        $result = WebService_Connector_Provider::connect('XmlRpc')
            ->getConnection()
            ->call(
                'call',
                array(
                    WebService_Connector_Provider::connect('XmlRpc')->getSession(),
                    'customer.create',
                    array($data['CustomerEntityToCreate']))
            );
        return $result;
    }

    public static function customerList($xmlPath)
    {
        $xml = simplexml_load_file($xmlPath);
        $data = WebService_Helper_Xml::simpleXMLToArray($xml);
        $result = WebService_Connector_Provider::connect('XmlRpc')
            ->getConnection()
            ->call(
                'call',
                array(
                   WebService_Connector_Provider::connect('XmlRpc')->getSession(),
                    'customer.list',
                array($data['filters']))
            );
        return WebService_Helper_Xml::applyIgnore($result, $data['ignore']);
    }

    public static function customerInfo($xmlPath)
    {
        $xml = simplexml_load_file($xmlPath);
        $data = WebService_Helper_Xml::simpleXMLToArray($xml);
        $result = WebService_Connector_Provider::connect('XmlRpc')
            ->getConnection()
            ->call(
                'call',
                array(
                   WebService_Connector_Provider::connect('XmlRpc')->getSession(),
                    'customer.info',
                array($data['parameters']))
            );
        return WebService_Helper_Xml::applyIgnore($result, $data['ignore']);
    }

    public static function customerUpdate($xmlPath)
    {
        $xml = simplexml_load_file($xmlPath);
        $data = WebService_Helper_Xml::simpleXMLToArray($xml);
        return WebService_Connector_Provider::connect('XmlRpc')
            ->getConnection()
            ->call(
                'call',
                array(
                    WebService_Connector_Provider::connect('XmlRpc')->getSession(),
                    'customer.update',
                array($data['parameters'], $data['CustomerEntityToUpdate']))
            );
    }

    public static function customerDelete($xmlPath)
    {
        $xml = simplexml_load_file($xmlPath);
        $data = WebService_Helper_Xml::simpleXMLToArray($xml);
        return WebService_Connector_Provider::connect('XmlRpc')
            ->getConnection()
            ->call(
                'call',
                array(
                    WebService_Connector_Provider::connect('XmlRpc')->getSession(),
                    'customer.delete',
                 array($data['parameters']))
            );
    }
}