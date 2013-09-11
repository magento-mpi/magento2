<?php
/**
 * Factory of web API requests.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webapi\Controller\Response;

class Factory
{
    /**
     * List of response classes corresponding to API types.
     *
     * @var array
     */
    protected $_apiResponseMap = array(
        \Magento\Webapi\Controller\Front::API_TYPE_REST => 'Magento\Webapi\Controller\Response\Rest',
        \Magento\Webapi\Controller\Front::API_TYPE_SOAP => 'Magento\Webapi\Controller\Response',
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
     * Create response object.
     *
     * Use current API type to define proper response class.
     *
     * @return \Magento\Webapi\Controller\Response
     * @throws \LogicException If there is no corresponding response class for current API type.
     */
    public function get()
    {
        $apiType = $this->_apiFrontController->determineApiType();
        if (!isset($this->_apiResponseMap[$apiType])) {
            throw new \LogicException(
                sprintf('There is no corresponding response class for the "%s" API type.', $apiType)
            );
        }
        $requestClass = $this->_apiResponseMap[$apiType];
        return $this->_objectManager->get($requestClass);
    }
}
