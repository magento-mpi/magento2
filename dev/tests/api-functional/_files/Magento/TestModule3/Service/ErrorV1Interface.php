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
     * @return \Magento\TestModule3\Service\Entity\V1\Parameter
     */
    public function success();

    /**
     * @return int Status
     */
    public function resourceNotFoundException();

    /**
     * @return int Status
     */
    public function serviceException();

    /**
     * @param \Magento\TestModule3\Service\Entity\V1\Parameter[] $parameters
     * @return int Status
     */
    public function parameterizedServiceException($parameters);

    /**
     * @return int Status
     */
    public function authorizationException();

    /**
     * @return int Status
     */
    public function webapiException();

    /**
     * @return int Status
     */
    public function otherException();

    /**
     * @return int Status
     */
    public function returnIncompatibleDataType();
}
