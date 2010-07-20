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
class WebService_Impl_Api_SoapV1_Service_Service

{
    public static function serviceConnect($xmlPath)
    {        
       return WebService_Service_Client::getSessionId();
    }

    public static function serviceDisconnect()
    {
        return WebService_Service_Client::disconnect();
    }
}