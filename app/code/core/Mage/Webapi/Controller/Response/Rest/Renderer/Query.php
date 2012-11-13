<?php
/**
 *  Query Renderer allows to format array or object as URL-encoded query string.
 *
 * @copyright {}
 */
class Mage_Webapi_Controller_Response_Rest_Renderer_Query implements
    Mage_Webapi_Controller_Response_Rest_RendererInterface
{
    /**
     * Adapter mime type.
     */
    const MIME_TYPE = 'text/plain';

    /**
     * Get query renderer MIME type.
     *
     * @return string
     */
    public function getMimeType()
    {
        return self::MIME_TYPE;
    }

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
}
