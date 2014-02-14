<?php
/**
 * Represents an endpoint to which messages can be sent
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Outbound
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Outbound;

interface EndpointInterface
{
    /**
     * Data formats
     */
    const FORMAT_JSON = 'json';
    const FORMAT_XML = 'xml';

    /** Authentication types */
    const AUTH_TYPE_HMAC = 'hmac';
    const AUTH_TYPE_NONE = 'none';

    /**
     * Returns the endpoint URL of this subscription
     *
     * @return string
     */
    public function getEndpointUrl();

    /**
     * Returns the maximum time in seconds that this subscription is willing to wait before a retry should be attempted
     *
     * @return int
     */
    public function getTimeoutInSecs();

    /**
     * Returns the format this message should be sent in (JSON, XML, etc.)
     *
     * @return string
     */
    public function getFormat();


    /**
     * Returns the user abstraction associated with this subscription or null if no user has been associated yet.
     *
     * @return \Magento\Outbound\UserInterface|null
     */
    public function getUser();

    /**
     * Returns the type of authentication to use when attaching authentication to a message
     *
     * @return string
     */
    public function getAuthenticationType();

}
