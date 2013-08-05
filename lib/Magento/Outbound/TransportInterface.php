<?php
/**
 * Interface for dispatching messages to subscribers
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Outbound
 * @copyright   {copyright}
 * @license     {license_link}
 */

interface Magento_Outbound_TransportInterface
{
    /**
     * Dispatch message and return response
     *
     * @param Magento_Outbound_MessageInterface $message
     * @return Magento_Outbound_Transport_Http_Response
     */
    public function dispatch(Magento_Outbound_MessageInterface $message);
}