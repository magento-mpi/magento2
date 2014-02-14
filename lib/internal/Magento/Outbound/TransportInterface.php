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

namespace Magento\Outbound;

interface TransportInterface
{
    /**
     * Dispatch message and return response
     *
     * @param \Magento\Outbound\MessageInterface $message
     * @return \Magento\Outbound\Transport\Http\Response
     */
    public function dispatch(\Magento\Outbound\MessageInterface $message);
}
