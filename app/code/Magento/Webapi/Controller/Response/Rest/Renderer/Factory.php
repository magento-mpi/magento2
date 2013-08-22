<?php
/**
 * Factory of REST renderers.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webapi_Controller_Response_Rest_Renderer_Factory
{
    /**
     * Response render adapters.
     */
    const XML_PATH_WEBAPI_RESPONSE_RENDERS = 'global/webapi/rest/response/renders';

    /**
     * @var Magento_ObjectManager
     */
    protected $_objectManager;

    /** @var Magento_Core_Model_Config */
    protected $_applicationConfig;

    /** @var Magento_Webapi_Controller_Request_Rest */
    protected $_request;

    /**
     * Initialize dependencies.
     *
     * @param Magento_ObjectManager $objectManager
     * @param Magento_Core_Model_Config $applicationConfig
     * @param Magento_Webapi_Controller_Request_Rest $request
     */
    public function __construct(
        Magento_ObjectManager $objectManager,
        Magento_Core_Model_Config $applicationConfig,
        Magento_Webapi_Controller_Request_Rest $request
    ) {
        $this->_objectManager = $objectManager;
        $this->_applicationConfig = $applicationConfig;
        $this->_request = $request;
    }

    /**
     * Get renderer for Mime-Type specified in Accept header of request.
     *
     * @return Magento_Webapi_Controller_Response_Rest_RendererInterface
     * @throws Magento_Webapi_Exception
     * @throws LogicException
     */
    public function get()
    {
        $acceptTypes = $this->_request->getAcceptTypes();
        $availableRenderers = (array)$this->_applicationConfig->getNode(self::XML_PATH_WEBAPI_RESPONSE_RENDERS);
        if (!is_array($acceptTypes)) {
            $acceptTypes = array($acceptTypes);
        }
        foreach ($acceptTypes as $acceptType) {
            foreach ($availableRenderers as $rendererConfig) {
                $rendererType = (string)$rendererConfig->type;
                if ($acceptType == $rendererType
                    || ($acceptType == current(explode('/', $rendererType)) . '/*')
                    || $acceptType == '*/*'
                ) {
                    $rendererClass = (string)$rendererConfig->model;
                    break 2;
                }
            }
        }
        if (!isset($rendererClass)) {
            /** If server does not have renderer for any of the accepted types it SHOULD send 406 (not acceptable). */
            throw new Magento_Webapi_Exception(
                __('Server cannot understand Accept HTTP header media type.'),
                Magento_Webapi_Exception::HTTP_NOT_ACCEPTABLE
            );
        }
        $renderer = $this->_objectManager->get($rendererClass);
        if (!$renderer instanceof Magento_Webapi_Controller_Response_Rest_RendererInterface) {
            throw new LogicException(
                'The renderer must implement "Magento_Webapi_Controller_Response_Rest_RendererInterface".');
        }
        return $renderer;
    }
}
