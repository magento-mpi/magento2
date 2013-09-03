<?php
/**
 * Creates new messages
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Outbound
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Outbound\Message;

class Factory implements \Magento\Outbound\Message\FactoryInterface
{
    /**
     * @var \Magento\ObjectManager
     */
    protected $_objectManager;

    /**
     * @var \Magento\Outbound\Formatter\Factory
     */
    private $_formatterFactory;

    /**
     * @var \Magento\Outbound\Authentication\Factory
     */
    private $_authFactory;

    /**
     * initialize the class
     *
     * @param \Magento\ObjectManager $objectManager
     * @param \Magento\Outbound\Formatter\Factory $formatterFactory
     * @param \Magento\Outbound\Authentication\Factory $authFactory
     */
    public function __construct(
        \Magento\ObjectManager $objectManager,
        \Magento\Outbound\Formatter\Factory $formatterFactory,
        \Magento\Outbound\Authentication\Factory $authFactory
    ) {
        $this->_objectManager = $objectManager;
        $this->_formatterFactory = $formatterFactory;
        $this->_authFactory = $authFactory;
    }

    /**
     * Create a message for a given endpoint, topic and message data
     *
     * @param \Magento\Outbound\EndpointInterface $endpoint
     * @param string                             $topic topic of the message
     * @param array                              $bodyData  body of the message
     *
     * @return \Magento\Outbound\Message
     */
    public function create(\Magento\Outbound\EndpointInterface $endpoint, $topic, array $bodyData)
    {
        // Format first since that should turn the body from an array into a string
        $formatter = $this->_formatterFactory->getFormatter($endpoint->getFormat());
        $headers = array(
            \Magento\Outbound\Message\FactoryInterface::TOPIC_HEADER => $topic,
            \Magento\Outbound\FormatterInterface::CONTENT_TYPE_HEADER => $formatter->getContentType(),
        );
        $formattedBody = $formatter->format($bodyData);

        $headers = array_merge(
            $headers,
            $this->_authFactory->getAuthentication($endpoint->getAuthenticationType())
                ->getSignatureHeaders($formattedBody, $endpoint->getUser())
        );

        return $this->_objectManager->create(
            '\Magento\Outbound\Message',
            array(
                 'endpointUrl' => $endpoint->getEndpointUrl(),
                 'headers'     => $headers,
                 'body'        => $formattedBody,
                 'timeout'     => $endpoint->getTimeoutInSecs(),
            )
        );
    }
}
