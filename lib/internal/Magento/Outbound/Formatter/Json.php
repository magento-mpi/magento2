<?php
/**
 * Formatter that converts an array into JSON string.
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Outbound
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Outbound\Formatter;

class Json implements \Magento\Outbound\FormatterInterface
{
    /**
     * The value for the Content-Type header for messages containing a JSON body
     */
    const CONTENT_TYPE = 'application/json';

    /**
     * Format the body of a message into JSON
     *
     * @param array $body
     * @throws \LogicException
     * @return string formatted body
     */
    public function format(array $body)
    {
        $formattedData = json_encode($body);
        if (false === $formattedData) {
            throw new \LogicException('The data provided cannot be converted to JSON.');
        }
        return $formattedData;
    }

    /**
     * Returns the content type for JSON formatting
     *
     * @return string 'application/json'
     */
    public function getContentType()
    {
        return self::CONTENT_TYPE;
    }
}
