<?php
/**
 * Interface for Messages that can be sent in PubSub
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Outbound
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Outbound;

interface MessageInterface
{
    /**
     * return endpoint url
     *
     * @return string
     */
    public function getEndpointUrl();

    /**
     * return formatted headers
     *
     * @return array
     */
    public function getHeaders();

    /**
     * Get the message body
     *
     * @return string|null
     */
    public function getBody();

    /**
     * Get timeout in seconds
     *
     * return timeout
     * @return int
     */
    public function getTimeout();
}
