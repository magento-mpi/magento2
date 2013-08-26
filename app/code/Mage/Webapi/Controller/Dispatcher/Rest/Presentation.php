<?php
/**
 * Helper for data processing according to REST presentation.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Webapi_Controller_Dispatcher_Rest_Presentation
{
    /** @var Mage_Webapi_Controller_Dispatcher_Rest_Presentation_Request */
    protected $_requestProcessor;

    /** @var Mage_Webapi_Controller_Dispatcher_Rest_Presentation_Response */
    protected $_responseProcessor;

    /**
     * Initialize dependencies.
     *
     * @param Mage_Webapi_Controller_Dispatcher_Rest_Presentation_Request $requestPresentation
     * @param Mage_Webapi_Controller_Dispatcher_Rest_Presentation_Response $responsePresentation
     */
    public function __construct(
        Mage_Webapi_Controller_Dispatcher_Rest_Presentation_Request $requestPresentation,
        Mage_Webapi_Controller_Dispatcher_Rest_Presentation_Response $responsePresentation
    ) {
        $this->_requestProcessor = $requestPresentation;
        $this->_responseProcessor = $responsePresentation;
    }

    /**
     * Fetch data from request.
     *
     * @return array
     */
    public function getRequestData()
    {
        return $this->_requestProcessor->getRequestData();
    }

    /**
     * Perform rendering of action results.
     *
     * @param array|null $outputData
     */
    public function prepareResponse($outputData = null)
    {
        $this->_responseProcessor->prepareResponse($outputData);
    }
}
