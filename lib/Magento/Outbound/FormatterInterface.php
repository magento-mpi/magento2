<?php
/**
 * Formats the body of a message
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Outbound
 * @copyright   {copyright}
 * @license     {license_link}
 */
interface Magento_Outbound_FormatterInterface
{
    /** content type header */
    const CONTENT_TYPE_HEADER = 'Content-Type';

    /**
     * Format the body of a message
     *
     * @param array $body
     * @return string formatted body
     */
    public function format(array $body);

    /**
     * Returns the content type for the specific formatter
     *
     * @return string A valid MIME-Type
     */
    public function getContentType();
}
