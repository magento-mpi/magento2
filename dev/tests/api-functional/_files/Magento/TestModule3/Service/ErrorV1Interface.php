<?php
/**
 * Interface for a test service for error handling testing
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\TestModule3\Service;

use Magento\TestModule3\Service\Entity\V1\Parameter;

interface ErrorV1Interface
{
    /**
     * @return Parameter
     */
    public function success();
    public function resourceNotFoundException();
    public function serviceException();
    public function parameterizedServiceException(array $parameters);
    public function authorizationException();
    public function webapiException();
    public function otherException();
    public function returnIncompatibleDataType();
}
