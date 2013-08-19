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

    /** Topic header */
    const TOPIC_HEADER = 'Magento-Topic';

    /**
     * Create a message for a given endpoint, topic and message data
     *
     * @param Magento_Outbound_EndpointInterface $endpoint
     * @param string $topic topic of the message
     * @param array $bodyData body of the message
     * @return Magento_Outbound_Message
     */
    public function create(Magento_Outbound_EndpointInterface $endpoint, $topic, array $bodyData);
}
