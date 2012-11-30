<?php
/**
 * Factory of web API requests.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Webapi_Controller_Request_Factory
{
    /**
     * List of request classes corresponding to API types.
     *
     * @var array
     */
    protected $_apiTypeToRequestMap = array(
        Mage_Webapi_Controller_Front::API_TYPE_REST => 'Mage_Webapi_Controller_Request_Rest',
        Mage_Webapi_Controller_Front::API_TYPE_SOAP => 'Mage_Webapi_Controller_Request_Soap',
    );

    /** @var Magento_ObjectManager */
    protected $_objectManager;

    /** @var Mage_Webapi_Controller_Front */
    protected $_apiFrontController;

    /**
     * Initialize dependencies.
     *
     * @param Mage_Webapi_Controller_Front $apiFrontController
     * @param Magento_ObjectManager $objectManager
     */
    public function __construct(
        Mage_Webapi_Controller_Front $apiFrontController,
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
     * @return Mage_Webapi_Controller_Request
     * @throws LogicException If there is no corresponding request class for current API type.
     */
    public function get()
    {
        $apiType = $this->_apiFrontController->determineApiType();
        if (!isset($this->_apiTypeToRequestMap[$apiType])) {
            throw new LogicException('There is no corresponding request class for the "%s" API type.', $apiType);
        }
        $requestClass = $this->_apiTypeToRequestMap[$apiType];
        return $this->_objectManager->get($requestClass);
    }
}
