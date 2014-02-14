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

interface FactoryInterface
{

    /** Topic header */
    const TOPIC_HEADER = 'Magento-Topic';

    /**
     * Create a message for a given endpoint, topic and message data
     *
     * @param \Magento\Outbound\EndpointInterface $endpoint
     * @param string $topic topic of the message
     * @param array $bodyData body of the message
     * @return \Magento\Outbound\Message
     */
    public function create(\Magento\Outbound\EndpointInterface $endpoint, $topic, array $bodyData);
}
