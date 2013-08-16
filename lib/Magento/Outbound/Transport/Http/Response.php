<?php
/**
 * Wrapper for Zend_Http_Response class, provides isSuccessful() method
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Outbound
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Outbound_Transport_Http_Response
{
    /**
     * @var Zend_Http_Response
     */
    protected $_response;

    public function __construct(Zend_Http_Response $response)
    {
        $this->_response = $response;
    }


    /**
     * Describes whether response code indicates success
     *
     * @return bool
     */
    public function isSuccessful()
    {
        $statusCode = $this->getStatusCode();
        if ($statusCode >= 200 && $statusCode < 300) {
            return true;
        }
        return false;
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
     * @return string
     */
    public function getBody()
    {
        // CURL Doesn't give us access to a truly RAW body, so calling getBody() will fail if Transfer-Encoding is set
        return $this->_response->getRawBody();
    }

    /**
     * Gets response body
     *
     * @return string
     */
    public function getRawBody()
    {
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
