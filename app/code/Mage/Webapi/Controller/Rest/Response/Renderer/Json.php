<?php
/**
 *  JSON Renderer allows to format array or object as JSON document.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Webapi_Controller_Rest_Response_Renderer_Json implements
    Mage_Webapi_Controller_Rest_Response_RendererInterface
{
    /**
     * Adapter mime type.
     */
    const MIME_TYPE = 'application/json';

    /** @var Mage_Core_Helper_Data */
    protected $_helper;

    /**
     * Initialize dependencies.
     *
     * @param Mage_Core_Model_Factory_Helper $helperFactory
     */
    public function __construct(
        Mage_Core_Model_Factory_Helper $helperFactory
    ) {
        $this->_helper = $helperFactory->get('Mage_Core_Helper_Data');
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
