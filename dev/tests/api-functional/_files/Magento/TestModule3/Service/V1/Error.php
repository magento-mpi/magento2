<?php
/**
 * Implementation of a test service for error handling testing
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\TestModule3\Service\V1;

use Magento\Exception\InputException;
use Magento\TestModule3\Service\V1\Entity\Parameter;
use Magento\TestModule3\Service\V1\Entity\ParameterBuilder;

class Error implements \Magento\TestModule3\Service\V1\ErrorInterface
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
        throw new \Magento\Webapi\ServiceResourceNotFoundException('', 2345, null, array(), 'resourceNotFound', 'resourceY');
    }

    /**
     * {@inheritdoc}
     */
    public function serviceException()
    {
        throw new \Magento\Webapi\ServiceException('Generic service exception', 3456);
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
        throw new \Magento\Webapi\ServiceException('Parameterized service exception', 1234, null, $details);
    }

    /**
     * {@inheritdoc}
     */
    public function authorizationException()
    {
        throw new \Magento\Webapi\ServiceAuthorizationException('', 4567, null, array(), 'authorization', 30, 'resourceN');
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

    /**
     * {@inheritdoc}
     */
    public function inputException($wrappedErrorParameters)
    {
        $exception = new InputException();
        if ($wrappedErrorParameters) {
            foreach ($wrappedErrorParameters as $error) {
                $exception->addError($error->getCode(), $error->getFieldName(), $error->getValue());
            }
        }
        throw $exception;
    }
}
