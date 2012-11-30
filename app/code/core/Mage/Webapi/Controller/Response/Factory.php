<?php
/**
 * Factory of web API requests.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Webapi_Controller_Response_Factory
{
    /**
     * List of response classes corresponding to API types.
     *
     * @var array
     */
    protected $_apiResponseMap = array(
        Mage_Webapi_Controller_Front::API_TYPE_REST => 'Mage_Webapi_Controller_Response_Rest',
        Mage_Webapi_Controller_Front::API_TYPE_SOAP => 'Mage_Webapi_Controller_Response',
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
     * Create response object.
     *
     * Use current API type to define proper response class.
     *
     * @return Mage_Webapi_Controller_Response
     * @throws LogicException If there is no corresponding response class for current API type.
     */
    public function get()
    {
        $apiType = $this->_apiFrontController->determineApiType();
        if (!isset($this->_apiResponseMap[$apiType])) {
            throw new LogicException('There is no corresponding response class for the "%s" API type.', $apiType);
        }
        $requestClass = $this->_apiResponseMap[$apiType];
        return $this->_objectManager->get($requestClass);
    }
}
