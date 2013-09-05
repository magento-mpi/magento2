<?php
/**
 * Wrapper for \Zend_Http_Response class, provides isSuccessful() method
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Outbound
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Outbound\Transport\Http;

class Response
{
    /**
     * @var \Zend_Http_Response $_response
     */
    protected $_response;

    /**
     * @param $string response string from an http request
     */
    public function __construct($string)
    {
        $this->_response = \Zend_Http_Response::fromString($string);
    }


    /**
     * Describes whether response code indicates success
     *
     * @return bool
     */
    public function isSuccessful()
    {
        return $this->_response->isSuccessful();
    }

    /**
     * Gets response status
     *
     * @return int
     */
    public function getStatusCode()
    {
        return $this->_response->getStatus();
    }

    /**
     * Gets response header
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->_response->getMessage();
    }

    /**
     * Gets response body
     *
     * This class is just hiding the 'getBody' function since calling that after our curl library has already decoded
     * the body, could cause an error. A perfect example is if the response for our curl call was gzip'ed, curl would
     * have gunzipped it but left the header indicating it was compressed, then \Zend_Http_Response::getBody() would
     * attempt to decompress the raw body, which was already decompressed, causing an error/corruption.
     *
     * @return string
     */
    public function getBody()
    {
        // CURL Doesn't give us access to a truly RAW body, so calling getBody() will fail if Transfer-Encoding is set
        return $this->_response->getRawBody();
    }

    /**
     * Gets response headers
     *
     * @return array
     */
    public function getHeaders()
    {
        return $this->_response->getHeaders();
    }

    /**
     * Gets a given response header
     *
     * @param string $headerName
     * @return array|null|string
     */
    public function getHeader($headerName)
    {
        return $this->_response->getHeader($headerName);
    }
}
