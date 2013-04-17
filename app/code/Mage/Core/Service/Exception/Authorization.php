<?php
/**
 * Authorization Exception.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Core_Service_Exception_Authorization extends Core_Service_Exception
{
    public function __construct($message = "", $code = 401, Exception $previous = null)
    {
        // force using 401 code
        parent::__construct($message, 401);
    }
}
