<?php
/**
 * {license_notice}
 *
 * @category   Magento
 * @package    Magento_HTTP
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * Magento HTTP Response Interface
 *
 * @category   Magento
 * @package    Magento_HTTP
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\HTTP;

/**
 * Class ResponseInterface
 * @package Magento\HTTP
 */
interface ResponseInterface
{
    /**
     * Set a header
     *
     * If $replace is true, replaces any headers already defined with that
     * $name.
     *
     * @param  string $name
     * @param  string $value
     * @param  boolean $replace
     * @return self
     */
    public function setHeader($name, $value, $replace = false);

    /**
     * Set redirect URL
     *
     * Sets Location header and response code. Forces replacement of any prior
     * redirects.
     *
     * @param  string $url
     * @param  int $code
     * @return self
     */
    public function setRedirect($url, $code = 302);

    /**
     * Is this a redirect?
     *
     * @return boolean
     */
    public function isRedirect();

    /**
     * Return array of headers; see {@link $_headers} for format
     *
     * @return array
     */
    public function getHeaders();

    /**
     * Get header value by name.
     *
     * @param string $name
     * @return array|bool
     */
    public function getHeader($name);

    /**
     * Clear headers
     *
     * @return self
     */
    public function clearHeaders();

    /**
     * Clears the specified HTTP header
     *
     * @param  string $name
     * @return self
     */
    public function clearHeader($name);

    /**
     * Set raw HTTP header
     *
     * Allows setting non key => value headers, such as status codes
     *
     * @param  string $value
     * @return self
     */
    public function setRawHeader($value);

    /**
     * Retrieve all {@link setRawHeader() raw HTTP headers}
     *
     * @return array
     */
    public function getRawHeaders();

    /**
     * Clear all {@link setRawHeader() raw HTTP headers}
     *
     * @return self
     */
    public function clearRawHeaders();

    /**
     * Clears the specified raw HTTP header
     *
     * @param  string $headerRaw
     * @return self
     */
    public function clearRawHeader($headerRaw);

    /**
     * Clear all headers, normal and raw
     *
     * @return self
     */
    public function clearAllHeaders();

    /**
     * Set HTTP response code to use with headers
     *
     * @param  int $code
     * @return self
     */
    public function setHttpResponseCode($code);

    /**
     * Retrieve HTTP response code
     *
     * @return int
     */
    public function getHttpResponseCode();

    /**
     * Can we send headers?
     *
     * @param  boolean $throw Whether or not to throw an exception if headers have been sent; defaults to false
     * @return boolean
     * @throws self
     */
    public function canSendHeaders($throw = false);

    /**
     * Send all headers
     *
     * Sends any headers specified. If an {@link setHttpResponseCode() HTTP response code}
     * has been specified, it is sent with the first header.
     *
     * @return self
     */
    public function sendHeaders();

    /**
     * Send response to client
     */
    public function sendResponse();
}
