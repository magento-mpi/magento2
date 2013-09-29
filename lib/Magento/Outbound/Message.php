<?php
/**
 * Message that can be sent to endpoints.
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Outbound
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Outbound;

class Message implements \Magento\Outbound\MessageInterface
{
    /** default timeout value in seconds */
    const DEFAULT_TIMEOUT = 20;

    /**
     * @var array
     */
    protected $_headers = array();

    /**
     * @var string|null
     */
    protected $_body;

    /**
     * @var int
     */
    protected $_timeout;

    /** @var string */
    protected $_endpointUrl;


    /**
     * @param string $endpointUrl
     * @param array $headers
     * @param null $body
     * @param int $timeout in seconds
     */
    public function __construct($endpointUrl, $headers = array(), $body = null, $timeout = self::DEFAULT_TIMEOUT)
    {
        $this->_endpointUrl = $endpointUrl;
        $this->_headers = $headers;
        $this->_body = $body;
        $this->_timeout = $timeout;
    }

    /**
     * return endpoint url
     *
     * @return string
     */
    public function getEndpointUrl()
    {
        return $this->_endpointUrl;
    }

    /**
     * Return headers array
     *
     * @return array
     */
    public function getHeaders()
    {
        return $this->_headers;
    }

    /**
     * return body
     *
     * @return string|null
     */
    public function getBody()
    {
        return $this->_body;
    }

    /**
     * return timeout in seconds
     *
     * @return int
     */
    public function getTimeout()
    {
        return $this->_timeout;
    }
}
