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
class Magento_Outbound_Message_Factory implements Magento_Outbound_Message_FactoryInterface
{
    /**
     * @var Magento_ObjectManager
     */
    protected $_objectManager;

    /**
     * @var Magento_Outbound_Formatter_Factory
     */
    private $_formatterFactory;

    /**
     * @var Magento_Outbound_Authentication_Factory
     */
    private $_authFactory;

    /**
     * initialize the class
     *
     * @param Magento_ObjectManager $objectManager
     * @param Magento_Outbound_Formatter_Factory $formatterFactory
     * @param Magento_Outbound_Authentication_Factory $authFactory
     */
    public function __construct(
        Magento_ObjectManager $objectManager,
        Magento_Outbound_Formatter_Factory $formatterFactory,
        Magento_Outbound_Authentication_Factory $authFactory
    ) {
        $this->_objectManager = $objectManager;
        $this->_formatterFactory = $formatterFactory;
        $this->_authFactory = $authFactory;
    }

    /**
     * Create a message for a given endpoint, topic and message data
     *
     * @param Magento_Outbound_EndpointInterface $endpoint
     * @param string                             $topic topic of the message
     * @param array                              $bodyData  body of the message
     *
     * @return Magento_Outbound_Message
     */
    public function create(Magento_Outbound_EndpointInterface $endpoint, $topic, array $bodyData)
    {
        // Format first since that should turn the body from an array into a string
        $formatter = $this->_formatterFactory->getFormatter($endpoint->getFormat());
        $headers = array(
            Magento_Outbound_Message_FactoryInterface::TOPIC_HEADER => $topic,
            Magento_Outbound_FormatterInterface::CONTENT_TYPE_HEADER => $formatter->getContentType(),
        );
        $formattedBody = $formatter->format($bodyData);

        $headers = array_merge(
            $headers,
            $this->_authFactory->getAuthentication($endpoint->getAuthenticationType())
                ->getSignatureHeaders($formattedBody, $endpoint->getUser())
        );

        return $this->_objectManager->create(
            'Magento_Outbound_Message',
            array(
                 'endpointUrl' => $endpoint->getEndpointUrl(),
                 'headers'     => $headers,
                 'body'        => $formattedBody,
                 'timeout'     => $endpoint->getTimeoutInSecs(),
            )
        );
    }
}
