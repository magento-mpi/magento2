<?php
/**
 * Interface for a test service for error handling testing
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
interface Magento_TestModule3_Service_ErrorV1Interface
{
    public function success();
    public function resourceNotFoundException();
    public function serviceException();
    public function parameterizedServiceException($parameters);
    public function authorizationException();
    public function webapiException();
    public function otherException();
    public function returnIncompatibleDataType();
}
