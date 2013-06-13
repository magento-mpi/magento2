<?php

class Mage_TestModule2_Service_V1_Error implements Mage_TestModule2_Service_V1_ErrorInterface
{
    public function success($request)
    {
        return array('id' => 'a good id');
    }

    public function resourceNotFoundException($request)
    {
        throw new Mage_Service_ResourceNotFoundException('Resource not found', 2345);
    }

    public function serviceException($request)
    {
        throw new Mage_Service_Exception('Generic service exception', 3456);
    }

	public function authorizationException($request)
    {
        throw new Mage_Service_AuthorizationException('Service authorization exception', 4567);
    }

    public function otherException($request)
    {
        throw new Exception('Non service exception', 5678);
    }
}
