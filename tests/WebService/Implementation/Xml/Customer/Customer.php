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
class WebService_Implementation_Xml_Customer_Customer
{
    public static function customerCreate($xmlPath)
    {
        $xml = simplexml_load_file($xmlPath);
        $result = (string)$xml->parameters->customer_id;
        return $result;
    }

    public static function customerList($xmlPath)
    {
        $xml = simplexml_load_file($xmlPath);     
        $data = WebService_Helper_Xml::simpleXMLToArray($xml);
        $result = WebService_Helper_Xml::applyIgnore($data['EntitiesList'],$data['ignore']);
        return $result;
    }

    public static function customerInfo($xmlPath)
    {
        $xml = simplexml_load_file($xmlPath);
        $data = WebService_Helper_Xml::simpleXMLToArray($xml);
        $result = WebService_Helper_Xml::applyIgnore($data['CustomerEntityInfo'],$data['ignore']);
        return $result;
    }

    public static function customerUpdate()
    {
        return true;
    }

    public static function customerDelete()
    {
        return true;
    }
}