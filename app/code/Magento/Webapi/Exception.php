<?php
/**
 * Webapi module exception. Should be used in web API resources implementation.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webapi;

class Exception extends \RuntimeException
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

    const ORIGINATOR_SENDER = 'Sender';
    const ORIGINATOR_RECEIVER = 'Receiver';

    /**
     * Initialize exception with HTTP code.
     *
     * @param string $message
     * @param int $code
     * @throws \InvalidArgumentException
     */
    public function __construct($message, $code)
    {
        /** Only HTTP error codes are allowed. No success or redirect codes must be used. */
        if ($code < 400 || $code > 599) {
            throw new \InvalidArgumentException(sprintf('The specified code "%d" is invalid.', $code));
        }
        parent::__construct($message, $code);
    }

    /**
     * Identify exception originator: sender or receiver.
     *
     * @return string
     */
    public function getOriginator()
    {
        return ($this->getCode() < 500) ? self::ORIGINATOR_SENDER : self::ORIGINATOR_RECEIVER;
    }
}
