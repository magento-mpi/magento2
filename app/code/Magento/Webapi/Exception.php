<?php
/**
 * Webapi module exception. Should be used in web API services implementation.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webapi_Exception extends RuntimeException
{
    /**#@+
     * Error HTTP response codes.
     */
    const HTTP_BAD_REQUEST = 400;
    const HTTP_UNAUTHORIZED = 401;
    const HTTP_FORBIDDEN = 403;
    const HTTP_NOT_FOUND = 404;
    const HTTP_METHOD_NOT_ALLOWED = 405;
    const HTTP_NOT_ACCEPTABLE = 406;
    const HTTP_INTERNAL_ERROR = 500;
    /**#@-*/

    /**
     * Optional exception details.
     *
     * @var array
     */
    protected $_details;

    /**
     * HTTP status code associated with current exception.
     *
     * @var int
     */
    protected $_httpCode;

    /**
     * Initialize exception with HTTP code.
     *
     * @param string $message
     * @param int $httpCode
     * @param int $code Error code
     * @param array $details Additional exception details
     * @throws InvalidArgumentException
     */
    public function __construct($message, $httpCode, $code = 0, array $details = array())
    {
        /** Only HTTP error codes are allowed. No success or redirect codes must be used. */
        if ($httpCode < 400 || $httpCode > 599) {
            throw new InvalidArgumentException(sprintf('The specified HTTP code "%d" is invalid.', $httpCode));
        }
        parent::__construct($message, $code);
        $this->_httpCode = $httpCode;
        $this->_details = $details;
    }

    /**
     * Retrieve current HTTP code.
     *
     * @return int
     */
    public function getHttpCode()
    {
        return $this->_httpCode;
    }

    /**
     * Identify exception originator: sender or receiver.
     *
     * @return string
     */
    public function getOriginator()
    {
        return ($this->getHttpCode() < 500)
            ? Mage_Webapi_Model_Soap_Fault::FAULT_CODE_SENDER
            : Mage_Webapi_Model_Soap_Fault::FAULT_CODE_RECEIVER;
    }

    /**
     * Retrieve exception details.
     *
     * @return array
     */
    public function getDetails()
    {
        return $this->_details;
    }
}
