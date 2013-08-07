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
interface Magento_Outbound_Message_FactoryInterface
{

    const TOPIC_HEADER = 'Magento-Topic';

    /**
     * Create a message for a given subscription and event
     *
     * @param Magento_Outbound_EndpointInterface $endpoint
     * @param Magento_PubSub_EventInterface $event
     * @return Magento_Outbound_Message
     */
    public function create(Magento_Outbound_EndpointInterface $endpoint, Magento_PubSub_EventInterface $event);

    /**
     * Create a message for a given subscription and message data
     *
     * @param Magento_Outbound_EndpointInterface $endpoint
     * @param string $topic topic of the message
     * @param array $bodyData body of the message
     * @return Magento_Outbound_Message
     */
    public function createByData(Magento_Outbound_EndpointInterface $endpoint, $topic, array $bodyData);
}
