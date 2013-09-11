<?php
/**
 * Factory of REST renders
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webapi_Controller_Response_Rest_Renderer_Factory
{
    /**
     * @var Magento_ObjectManager
     */
    protected $_objectManager;

    /**
     * @var Magento_Webapi_Controller_Request_Rest
     */
    protected $_request;

    /**
     * @var array
     */
    protected $_renders;

    /**
     * @param Magento_ObjectManager $objectManager
     * @param Magento_Webapi_Controller_Request_Rest $request
     * @param array $renders
     */
    public function __construct(
        Magento_ObjectManager $objectManager,
        Magento_Webapi_Controller_Request_Rest $request,
        array $renders = array()
    ) {
        $this->_objectManager = $objectManager;
        $this->_request = $request;
        $this->_renders = $renders;
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
        if (!is_array($acceptTypes)) {
            $acceptTypes = array($acceptTypes);
        }
        foreach ($acceptTypes as $acceptType) {
            foreach ($this->_renders as $rendererConfig) {
                $rendererType = $rendererConfig['type'];
                if ($acceptType == $rendererType
                    || ($acceptType == current(explode('/', $rendererType)) . '/*')
                    || $acceptType == '*/*'
                ) {
                    $rendererClass = $rendererConfig['model'];
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
