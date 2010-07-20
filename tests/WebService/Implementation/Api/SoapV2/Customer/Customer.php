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
class WebService_Implementation_Api_SoapV2_Customer_Customer
{
    public static function customerCreate($xmlPath)
    {
        $xml = simplexml_load_file($xmlPath);
        $data = WebService_Helper_Xml::simpleXMLToArray($xml);
        $result = WebService_Connector_Provider::connect('SoapV2')
            ->getConnection()
            ->customerCustomerCreate(
                WebService_Connector_Provider::connect('SoapV2')->getSession(),
                (object) $data['CustomerEntityToCreate']
            );
        return $result;

    }

    public static function customerList($xmlPath)
    {
        $xml = simplexml_load_file($xmlPath);
        $data = WebService_Helper_Xml::simpleXMLToArray($xml);
        $result = WebService_Connector_Provider::connect('SoapV2')
            ->getConnection()
            ->customerCustomerList(
                WebService_Connector_Provider::connect('SoapV2')->getSession(),
                WebService_Helper_Xml::getFilter($xmlPath)
        );
        $result = WebService_Helper_Xml::getArrayFromObject($result);
        $result = WebService_Helper_Xml::applyIgnore($result, $data['ignore']) ;
        return $result;
    }

    public static function customerInfo($xmlPath)
    {
        $xml = simplexml_load_file($xmlPath);
        $data = WebService_Helper_Xml::simpleXMLToArray($xml);
        $result = WebService_Connector_Provider::connect('SoapV2')
            ->getConnection()
            ->customerCustomerInfo(
                WebService_Connector_Provider::connect('SoapV2')->getSession(),
                (string)$xml->parameters->customer_id,
                array_keys(WebService_Helper_Xml::applyIgnore($data['CustomerEntityInfo'],$data['ignore']))
        );

        return get_object_vars($result);
    }

    public static function customerUpdate($xmlPath)
    {
        $xml = simplexml_load_file($xmlPath);
        $data = WebService_Helper_Xml::simpleXMLToArray($xml);
        return WebService_Connector_Provider::connect('SoapV2')
            ->getConnection()
            ->customerCustomerUpdate(
                WebService_Connector_Provider::connect('SoapV2')->getSession(),
                $xml->parameters->customer_id,
                (object)$data['CustomerEntityToUpdate']
        );
        return true;
    }

    public static function customerDelete($xmlPath)
    {
        $xml = simplexml_load_file($xmlPath);
        $data = WebService_Helper_Xml::simpleXMLToArray($xml);
        return WebService_Connector_Provider::connect('SoapV2')
            ->getConnection()
            ->customerCustomerDelete(
                WebService_Connector_Provider::connect('SoapV2')->getSession(),
                $xml->parameters->customer_id
        );
        return true;
    }
}
