<?php
/**
 * Service exception. Should be used within services implementation.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Core_Service_Exception extends RuntimeException
{
    /**
     * @var int $_httpErrorCode
     */
    protected $_httpErrorCode = null;

    /**
     * Initialize exception with HTTP code.
     *
     * @param string $message [optional] The Exception message to throw.
     * @param int $code [optional] The Exception code.
     * @param Exception $previous [optional] The previous exception used for the exception chaining. Since 5.3.0
     * @param $forcedHttpErrorCode [optional] HTTP Error code.
     */
    public function __construct($message = "", $code = 0, Exception $previous = null, $httpErrorCode = null)
    {
        $this->_httpErrorCode = $httpErrorCode;

        parent::__construct($message, $code, $previous);
    }

    /**
     * @return int|null
     */
    public function getHttpErrorCode()
    {
        return $this->_httpErrorCode;
    }
}
