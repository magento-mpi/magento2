<?php
/**
 * Factory of web API requests.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webapi_Controller_Response_Factory
{
    /**
     * List of response classes corresponding to API types.
     *
     * @var array
     */
    protected $_apiResponseMap = array(
        Magento_Webapi_Controller_Front::API_TYPE_REST => 'Magento_Webapi_Controller_Response_Rest',
        Magento_Webapi_Controller_Front::API_TYPE_SOAP => 'Magento_Webapi_Controller_Response',
    );

    /** @var \Magento\ObjectManager */
    protected $_objectManager;

    /** @var Magento_Webapi_Controller_Front */
    protected $_apiFrontController;

    /**
     * Initialize dependencies.
     *
     * @param Magento_Webapi_Controller_Front $apiFrontController
     * @param \Magento\ObjectManager $objectManager
     */
    public function __construct(
        Magento_Webapi_Controller_Front $apiFrontController,
        \Magento\ObjectManager $objectManager
    ) {
        $this->_apiFrontController = $apiFrontController;
        $this->_objectManager = $objectManager;
    }

    /**
     * Create response object.
     *
     * Use current API type to define proper response class.
     *
     * @return Magento_Webapi_Controller_Response
     * @throws LogicException If there is no corresponding response class for current API type.
     */
    public function get()
    {
        $apiType = $this->_apiFrontController->determineApiType();
        if (!isset($this->_apiResponseMap[$apiType])) {
            throw new LogicException(
                sprintf('There is no corresponding response class for the "%s" API type.', $apiType)
            );
        }
        $requestClass = $this->_apiResponseMap[$apiType];
        return $this->_objectManager->get($requestClass);
    }
}
