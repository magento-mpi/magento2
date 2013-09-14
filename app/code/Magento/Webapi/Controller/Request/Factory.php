<?php
/**
 * Factory of web API requests.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webapi\Controller\Request;

class Factory
{
    /**
     * List of request classes corresponding to API types.
     *
     * @var array
     */
    protected $_apiTypeToRequestMap = array(
        \Magento\Webapi\Controller\Front::API_TYPE_REST => 'Magento\Webapi\Controller\Request\Rest',
        \Magento\Webapi\Controller\Front::API_TYPE_SOAP => 'Magento\Webapi\Controller\Request\Soap',
    );

    /** @var \Magento\ObjectManager */
    protected $_objectManager;

    /** @var \Magento\Webapi\Controller\Front */
    protected $_apiFrontController;

    /**
     * Initialize dependencies.
     *
     * @param \Magento\Webapi\Controller\Front $apiFrontController
     * @param \Magento\ObjectManager $objectManager
     */
    public function __construct(
        \Magento\Webapi\Controller\Front $apiFrontController,
        \Magento\ObjectManager $objectManager
    ) {
        $this->_apiFrontController = $apiFrontController;
        $this->_objectManager = $objectManager;
    }

    /**
     * Create request object.
     *
     * Use current API type to define proper request class.
     *
     * @return \Magento\Webapi\Controller\Request
     * @throws \LogicException If there is no corresponding request class for current API type.
     */
    public function get()
    {
        $apiType = $this->_apiFrontController->determineApiType();
        if (!isset($this->_apiTypeToRequestMap[$apiType])) {
            throw new \LogicException(
                sprintf('There is no corresponding request class for the "%s" API type.', $apiType)
            );
        }
        $requestClass = $this->_apiTypeToRequestMap[$apiType];
        return $this->_objectManager->get($requestClass);
    }
}
