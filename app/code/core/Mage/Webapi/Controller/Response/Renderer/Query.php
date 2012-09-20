<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Webapi
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * Webapi renderer for URL-encoded query string format.
 *
 * @category   Mage
 * @package    Mage_Webapi
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Webapi_Controller_Response_Renderer_Query implements Mage_Webapi_Controller_Response_RendererInterface
{
    /**
     * Adapter mime type.
     */
    const MIME_TYPE = 'text/plain';

    /**
     * Convert data to URL-encoded query string format.
     *
     * @param array|object $data
     * @return string
     */
    public function render($data)
    {
        return http_build_query($data);
    }

    /**
     * Get query renderer MIME type.
     *
     * @return string
     */
    public function getMimeType()
    {
        return self::MIME_TYPE;
    }
}
