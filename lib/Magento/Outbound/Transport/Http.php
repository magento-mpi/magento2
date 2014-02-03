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

use Magento\HTTP\Adapter\Curl;
use Magento\Outbound\Message;
use Magento\Outbound\MessageInterface;
use Magento\Outbound\TransportInterface;
use Magento\Outbound\Transport\Http\Response;

class Http implements TransportInterface
{
    /**
     * Http version used by Magento
     */
    const HTTP_VERSION = '1.1';

    /**
     * @var Curl
     */
    protected $_curl;

    /**
     * @param Curl $curl
     */
    public function __construct(Curl $curl)
    {
        $this->_curl = $curl;
    }

    /**
     * Dispatch message and return response
     *
     * @param MessageInterface $message
     * @return Response
     */
    public function dispatch(MessageInterface $message)
    {
        $config = array(
            'verifypeer' => TRUE,
            'verifyhost' => 2
        );

        $timeout = $message->getTimeout();
        if (!is_null($timeout) && $timeout > 0) {
            $config['timeout'] = $timeout;
        } else {
            $config['timeout'] = Message::DEFAULT_TIMEOUT;
        }
        $this->_curl->setConfig($config);

        $this->_curl->write(\Zend_Http_Client::POST,
            $message->getEndpointUrl(),
            self::HTTP_VERSION,
            $this->_prepareHeaders($message->getHeaders()),
            $message->getBody()
        );

        return new Response($this->_curl->read());
    }

    /**
     * Prepare headers for dispatch
     *
     * @param string[] $headers
     * @return string[]
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
