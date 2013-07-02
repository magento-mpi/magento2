<?php
/**
 * Implementation of a test service for error handling testing
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_TestModule3_Service_ErrorV1 implements Mage_TestModule3_Service_ErrorInterfaceV1
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
