<?php
/**
 * Dispatches messages over HTTP
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Outbound
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Outbound\Transport;

class Http implements \Magento\Outbound\TransportInterface
{
    /**
     * Http version used by Magento
     */
    const HTTP_VERSION = '1.1';

    /**
     * @var \Magento\HTTP\Adapter\Curl
     */
    protected $_curl;

    /**
     * @param \Magento\HTTP\Adapter\Curl $curl
     */
    public function __construct(\Magento\HTTP\Adapter\Curl $curl)
    {
        $this->_curl = $curl;
    }

    /**
     * Dispatch message and return response
     *
     * @param \Magento\Outbound\MessageInterface $message
     * @return \Magento\Outbound\Transport\Http\Response
     */
    public function dispatch(\Magento\Outbound\MessageInterface $message)
    {
        $config = array(
            'verifypeer' => TRUE,
            'verifyhost' => 2
        );

        $timeout = $message->getTimeout();
        if (!is_null($timeout) && $timeout > 0) {
            $config['timeout'] = $timeout;
        } else {
            $config['timeout'] = \Magento\Outbound\Message::DEFAULT_TIMEOUT;
        }
        $this->_curl->setConfig($config);

        $this->_curl->write(\Zend_Http_Client::POST,
            $message->getEndpointUrl(),
            self::HTTP_VERSION,
            $this->_prepareHeaders($message->getHeaders()),
            $message->getBody()
        );

        return new \Magento\Outbound\Transport\Http\Response($this->_curl->read());
    }

    /**
     * Prepare headers for dispatch
     *
     * @param string[] $headers
     * @return array
     */
    protected function _prepareHeaders($headers)
    {
        $result = array();
        foreach ($headers as $headerName => $headerValue) {
            $result[] = sprintf('%s: %s', $headerName, $headerValue);
        }
        return $result;
    }
}
