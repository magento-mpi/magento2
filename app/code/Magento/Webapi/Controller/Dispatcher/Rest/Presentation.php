<?php
/**
 * Helper for data processing according to REST presentation.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webapi_Controller_Dispatcher_Rest_Presentation
{
    /** @var Magento_Webapi_Controller_Dispatcher_Rest_Presentation_Request */
    protected $_requestProcessor;

    /** @var Magento_Webapi_Controller_Dispatcher_Rest_Presentation_Response */
    protected $_responseProcessor;

    /**
     * Initialize dependencies.
     *
     * @param Magento_Webapi_Controller_Dispatcher_Rest_Presentation_Request $requestPresentation
     * @param Magento_Webapi_Controller_Dispatcher_Rest_Presentation_Response $responsePresentation
     */
    public function __construct(
        Magento_Webapi_Controller_Dispatcher_Rest_Presentation_Request $requestPresentation,
        Magento_Webapi_Controller_Dispatcher_Rest_Presentation_Response $responsePresentation
    ) {
        $this->_requestProcessor = $requestPresentation;
        $this->_responseProcessor = $responsePresentation;
    }

    /**
     * Fetch data from request and prepare it for passing to specified action.
     *
     * @param object $controllerInstance
     * @param string $action
     * @return array
     */
    public function fetchRequestData($controllerInstance, $action)
    {
        return $this->_requestProcessor->fetchRequestData($controllerInstance, $action);
    }

    /**
     * Perform rendering of action results.
     *
     * @param string $method
     * @param array|null $outputData
     */
    public function prepareResponse($method, $outputData = null)
    {
        $this->_responseProcessor->prepareResponse($method, $outputData);
    }
}
