<?php
/**
 * Factory of web API requests.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webapi_Controller_Request_Factory
{
    /**
     * List of request classes corresponding to API types.
     *
     * @var array
     */
    protected $_apiTypeToRequestMap = array(
        Magento_Webapi_Controller_Front::API_TYPE_REST => 'Magento_Webapi_Controller_Request_Rest',
        Magento_Webapi_Controller_Front::API_TYPE_SOAP => 'Magento_Webapi_Controller_Request_Soap',
    );

    /** @var Magento_ObjectManager */
    protected $_objectManager;

    /** @var Magento_Webapi_Controller_Front */
    protected $_apiFrontController;

    /**
     * Initialize dependencies.
     *
     * @param Magento_Webapi_Controller_Front $apiFrontController
     * @param Magento_ObjectManager $objectManager
     */
    public function __construct(
        Magento_Webapi_Controller_Front $apiFrontController,
        Magento_ObjectManager $objectManager
    ) {
        $this->_apiFrontController = $apiFrontController;
        $this->_objectManager = $objectManager;
    }

    /**
     * Create request object.
     *
     * Use current API type to define proper request class.
     *
     * @return Magento_Webapi_Controller_Request
     * @throws LogicException If there is no corresponding request class for current API type.
     */
    public function get()
    {
        $apiType = $this->_apiFrontController->determineApiType();
        if (!isset($this->_apiTypeToRequestMap[$apiType])) {
            throw new LogicException(
                sprintf('There is no corresponding request class for the "%s" API type.', $apiType)
            );
        }
        $requestClass = $this->_apiTypeToRequestMap[$apiType];
        return $this->_objectManager->get($requestClass);
    }
}
