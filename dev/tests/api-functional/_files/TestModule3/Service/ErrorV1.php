<?php
/**
 * Implementation of a test service for error handling testing
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_TestModule3_Service_ErrorV1 implements Magento_TestModule3_Service_ErrorV1Interface
{
    public function success()
    {
        return array('id' => 'a good id');
    }

    public function resourceNotFoundException()
    {
        throw new \Magento\Service\ResourceNotFoundException('', 2345, null, array(), 'resourceY');
    }

    public function serviceException()
    {
        throw new \Magento\Service\Exception('Generic service exception', 3456);
    }

    public function parameterizedServiceException($parameters)
    {
        throw new \Magento\Service\Exception('Parameterized service exception', 1234, null, $parameters['details']);
    }

    public function authorizationException()
    {
        throw new \Magento\Service\AuthorizationException('', 4567, null, array(), 30, 'resourceN');
    }

    public function webapiException()
    {
        throw new \Magento\Webapi\Exception('Service not found', 5555, \Magento\Webapi\Exception::HTTP_NOT_FOUND);
    }

    public function otherException()
    {
        throw new Exception('Non service exception', 5678);
    }

    public function returnIncompatibleDataType()
    {
        return "incompatibleDataType";
    }
}
