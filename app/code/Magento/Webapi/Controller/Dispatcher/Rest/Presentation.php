<?php
/**
 * Helper for data processing according to REST presentation.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webapi\Controller\Dispatcher\Rest;

class Presentation
{
    /** @var \Magento\Webapi\Controller\Dispatcher\Rest\Presentation\Request */
    protected $_requestProcessor;

    /** @var \Magento\Webapi\Controller\Dispatcher\Rest\Presentation\Response */
    protected $_responseProcessor;

    /**
     * Initialize dependencies.
     *
     * @param \Magento\Webapi\Controller\Dispatcher\Rest\Presentation\Request $requestPresentation
     * @param \Magento\Webapi\Controller\Dispatcher\Rest\Presentation\Response $responsePresentation
     */
    public function __construct(
        \Magento\Webapi\Controller\Dispatcher\Rest\Presentation\Request $requestPresentation,
        \Magento\Webapi\Controller\Dispatcher\Rest\Presentation\Response $responsePresentation
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
