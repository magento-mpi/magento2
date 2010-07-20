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
class WebService_Implementation_Unit_Customer_Customer
{
    public static function customerCreate($xmlPath)
    {
        $customer = Mage::getModel('customer/customer_api');
        $xml = simplexml_load_file($xmlPath);
        $data = WebService_Helper_Xml::simpleXMLToArray($xml);
        return $customer->create($data['CustomerEntityToCreate']);
    }

    public static function customerList($xmlPath)
    {
        $customer = Mage::getModel('customer/customer_api');
        $xml = simplexml_load_file($xmlPath);
        $data = WebService_Helper_Xml::simpleXMLToArray($xml);
        $result = WebService_Helper_Xml::applyIgnore($customer->items($data['filters']),$data['ignore']);
        return $result;
    }

    public static function customerInfo($xmlPath)
    {
        $customer = Mage::getModel('customer/customer_api');
        $xml = simplexml_load_file($xmlPath);
        $data = WebService_Helper_Xml::simpleXMLToArray($xml);
        $result = WebService_Helper_Xml::applyIgnore($customer->info($data['parameters']),$data['ignore'], 1);
        return $result;
    }

    public static function customerUpdate($xmlPath)
    {
        $customer = Mage::getModel('customer/customer_api');
        $xml = simplexml_load_file($xmlPath);
        $data = WebService_Helper_Xml::simpleXMLToArray($xml);
        return $customer->update($data['parameters'], $data['CustomerEntityToUpdate']);
    }

    public static function customerDelete($xmlPath)
    {
        $customer = Mage::getModel('customer/customer_api');
        $xml = simplexml_load_file($xmlPath);
        $data = WebService_Helper_Xml::simpleXMLToArray($xml);
        return $customer->delete($data['parameters']);
    }
}
