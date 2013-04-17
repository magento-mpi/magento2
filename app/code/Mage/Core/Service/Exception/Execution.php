<?php
/**
 * Execution Exception.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Core_Service_Exception_Execution extends Core_Service_Exception
{
    public function __construct($message = "", $code = 500, Exception $previous = null)
    {
        // force using 500 code
        parent::__construct($message, 500);
    }
}
