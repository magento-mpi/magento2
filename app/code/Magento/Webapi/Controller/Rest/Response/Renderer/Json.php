<?php
/**
 *  JSON Renderer allows to format array or object as JSON document.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webapi_Controller_Rest_Response_Renderer_Json implements
    Magento_Webapi_Controller_Rest_Response_RendererInterface
{
    /**
     * Adapter mime type.
     */
    const MIME_TYPE = 'application/json';

    /** @var Magento_Core_Helper_Data */
    protected $_helper;

    /**
     * Initialize dependencies.
     *
     * @param Magento_Core_Helper_Data $helper
     */
    public function __construct(Magento_Core_Helper_Data $helper)
    {
        $this->_helper = $helper;
    }

    /**
     * Convert data to JSON.
     *
     * @param array|object $data
     * @return string
     */
    public function render($data)
    {
        return $this->_helper->jsonEncode($data);
    }

    /**
     * Get JSON renderer MIME type.
     *
     * @return string
     */
    public function getMimeType()
    {
        return self::MIME_TYPE;
    }
}
