<?php
/**
 * Interface for a test service for error handling testing
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
interface Mage_TestModule3_Service_ErrorInterfaceV1
{
    public function success();
    public function resourceNotFoundException();
    public function serviceException();
    public function parameterizedException($params);
    public function authorizationException();
    public function otherException();
}
