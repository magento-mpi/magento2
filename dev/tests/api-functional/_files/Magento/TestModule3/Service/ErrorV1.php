<?php
/**
 * Implementation of a test service for error handling testing
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\TestModule3\Service;

use Magento\TestModule3\Service\Entity\V1\Parameter;
use Magento\TestModule3\Service\Entity\V1\ParameterBuilder;

class ErrorV1 implements \Magento\TestModule3\Service\ErrorV1Interface
{
    /**
     * {@inheritdoc}
     */
    public function success()
    {
        return (new ParameterBuilder())->setName('id')->setValue('a good id')->create();
    }

    /**
     * {@inheritdoc}
     */
    public function resourceNotFoundException()
    {
        throw new \Magento\Service\ResourceNotFoundException('', 2345, null, array(), 'resourceNotFound', 'resourceY');
    }

    /**
     * {@inheritdoc}
     */
    public function serviceException()
    {
        throw new \Magento\Service\Exception('Generic service exception', 3456);
    }

    /**
     * {@inheritdoc}
     */
    public function parameterizedServiceException($parameters)
    {
        $details = array();
        foreach ($parameters as $parameter) {
            $details[$parameter->getName()] = $parameter->getValue();
        }
        throw new \Magento\Service\Exception('Parameterized service exception', 1234, null, $details);
    }

    /**
     * {@inheritdoc}
     */
    public function authorizationException()
    {
        throw new \Magento\Service\AuthorizationException('', 4567, null, array(), 'authorization', 30, 'resourceN');
    }

    /**
     * {@inheritdoc}
     */
    public function webapiException()
    {
        throw new \Magento\Webapi\Exception('Service not found', 5555, \Magento\Webapi\Exception::HTTP_NOT_FOUND);
    }

    /**
     * {@inheritdoc}
     */
    public function otherException()
    {
        throw new \Exception('Non service exception', 5678);
    }

    /**
     * {@inheritdoc}
     */
    public function returnIncompatibleDataType()
    {
        return "incompatibleDataType";
    }
}
