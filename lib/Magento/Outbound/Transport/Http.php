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
class Magento_Outbound_Transport_Http implements Magento_Outbound_TransportInterface
{
    /**
     * Http version used by Magento
     */
    const HTTP_VERSION = '1.1';

    /**
     * @var Magento_HTTP_Adapter_Curl
     */
    protected $_curl;

    /**
     * @param Magento_HTTP_Adapter_Curl $curl
     */
    public function __construct(Magento_HTTP_Adapter_Curl $curl)
    {
        $this->_curl = $curl;
    }

    /**
     * Dispatch message and return response
     *
     * @param Magento_Outbound_MessageInterface $message
     * @return Magento_Outbound_Transport_Http_Response
     */
    public function dispatch(Magento_Outbound_MessageInterface $message)
    {
        $config = array(
            'verifypeer' => TRUE,
            'verifyhost' => 2
        );

        $timeout = $message->getTimeout();
        if (!is_null($timeout) && $timeout > 0) {
            $config['timeout'] = $timeout;
        } else {
            $config['timeout'] = Magento_Outbound_Message::DEFAULT_TIMEOUT;
        }
        $this->_curl->setConfig($config);

        $this->_curl->write(Zend_Http_Client::POST,
            $message->getEndpointUrl(),
            self::HTTP_VERSION,
            $this->_prepareHeaders($message->getHeaders()),
            $message->getBody()
        );

        return new Magento_Outbound_Transport_Http_Response($this->_curl->read());
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
