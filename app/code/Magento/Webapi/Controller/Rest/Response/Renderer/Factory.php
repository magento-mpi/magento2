<?php
/**
 * Factory of REST renderers.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webapi\Controller\Rest\Response\Renderer;

class Factory
{
    /**
     * Response render adapters.
     */
    const XML_PATH_WEBAPI_RESPONSE_RENDERS = 'global/webapi/rest/response/renders';

    /**
     * @var \Magento\ObjectManager
     */
    protected $_objectManager;

    /** @var \Magento\Core\Model\Config */
    protected $_applicationConfig;

    /** @var \Magento\Webapi\Controller\Rest\Request */
    protected $_request;

    /**
     * Initialize dependencies.
     *
     * @param \Magento\ObjectManager $objectManager
     * @param \Magento\Core\Model\Config $applicationConfig
     * @param \Magento\Webapi\Controller\Rest\Request $request
     */
    public function __construct(
        \Magento\ObjectManager $objectManager,
        \Magento\Core\Model\Config $applicationConfig,
        \Magento\Webapi\Controller\Rest\Request $request
    ) {
        $this->_objectManager = $objectManager;
        $this->_applicationConfig = $applicationConfig;
        $this->_request = $request;
    }

    /**
     * Get renderer for Mime-Type specified in Accept header of request.
     *
     * @return \Magento\Webapi\Controller\Rest\Response\RendererInterface
     * @throws \Magento\Webapi\Exception
     * @throws \LogicException
     */
    public function get()
    {
        $renderer = $this->_objectManager->get($this->_getRendererClass());
        if (!$renderer instanceof \Magento\Webapi\Controller\Rest\Response\RendererInterface) {
            throw new \LogicException(
                'The renderer must implement "Magento\Webapi\Controller\Rest\Response\RendererInterface".');
        }
        return $renderer;
    }

    /**
     * Find renderer which can render response in requested format.
     *
     * @return string
     * @throws \Magento\Webapi\Exception
     */
    protected function _getRendererClass()
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
                    return (string)$rendererConfig->model;
                }
            }
        }
        /** If server does not have renderer for any of the accepted types it SHOULD send 406 (not acceptable). */
        throw new \Magento\Webapi\Exception(
            __('Server cannot understand Accept HTTP header media type.'),
            0,
            \Magento\Webapi\Exception::HTTP_NOT_ACCEPTABLE
        );
    }
}
