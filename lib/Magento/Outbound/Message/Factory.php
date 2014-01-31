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

use Magento\ObjectManager;
use Magento\Outbound\EndpointInterface;
use Magento\Outbound\FormatterInterface;
use Magento\Outbound\Message;
use Magento\Outbound\Authentication\Factory as AuthenticationFactory;
use Magento\Outbound\Formatter\Factory as FormatterFactory;

class Factory implements FactoryInterface
{
    /**
     * @var ObjectManager
     */
    protected $_objectManager;

    /**
     * @var FormatterFactory
     */
    private $_formatterFactory;

    /**
     * @var AuthenticationFactory
     */
    private $_authFactory;

    /**
     * initialize the class
     *
     * @param ObjectManager $objectManager
     * @param FormatterFactory $formatterFactory
     * @param AuthenticationFactory $authFactory
     */
    public function __construct(
        ObjectManager $objectManager,
        FormatterFactory $formatterFactory,
        AuthenticationFactory $authFactory
    ) {
        $this->_objectManager = $objectManager;
        $this->_formatterFactory = $formatterFactory;
        $this->_authFactory = $authFactory;
    }

    /**
     * Create a message for a given endpoint, topic and message data
     *
     * @param EndpointInterface $endpoint
     * @param string $topic topic of the message
     * @param array $bodyData body of the message
     * @return Message
     */
    public function create(EndpointInterface $endpoint, $topic, array $bodyData)
    {
        // Format first since that should turn the body from an array into a string
        $formatter = $this->_formatterFactory->getFormatter($endpoint->getFormat());
        $headers = array(
            FactoryInterface::TOPIC_HEADER => $topic,
            FormatterInterface::CONTENT_TYPE_HEADER => $formatter->getContentType(),
        );
        $formattedBody = $formatter->format($bodyData);

        $headers = array_merge(
            $headers,
            $this->_authFactory->getAuthentication($endpoint->getAuthenticationType())
                ->getSignatureHeaders($formattedBody, $endpoint->getUser())
        );

        return $this->_objectManager->create(
            'Magento\Outbound\Message',
            array(
                 'endpointUrl' => $endpoint->getEndpointUrl(),
                 'headers'     => $headers,
                 'body'        => $formattedBody,
                 'timeout'     => $endpoint->getTimeoutInSecs(),
            )
        );
    }
}
