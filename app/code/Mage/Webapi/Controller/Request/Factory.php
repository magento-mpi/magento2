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
    /** @var Magento_ObjectManager */
    protected $_objectManager;

    /** @var Mage_Core_Model_App */
    protected $_application;

    /** @var Mage_Core_Model_Config */
    protected $_config;

    /**
     * Initialize dependencies.
     *
     * @param Mage_Core_Model_App $application
     * @param Mage_Core_Model_Config $config
     * @param Magento_ObjectManager $objectManager
     */
    public function __construct(
        Mage_Core_Model_App $application,
        Mage_Core_Model_Config $config,
        Magento_ObjectManager $objectManager
    ) {
        $this->_application = $application;
        $this->_config = $config;
        $this->_objectManager = $objectManager;
    }

    /**
     * Determine request type (e.g. SOAP or REST) and return request object.
     *
     * @return Mage_Webapi_Controller_Request
     * @throws LogicException If there is no corresponding request class for current request type.
     */
    public function get()
    {
        $pathInfo = ltrim($this->_application->getRequest()->getOriginalPathInfo(), '/');

        if (!preg_match('/\w+/', $pathInfo, $matches)) {
            throw new \LogicException('Invalid request: valid request type expected.');
        }

        $requestType = strtolower($matches[0]);
        $requestClass = sprintf('Mage_Webapi_Controller_%s_Request', ucfirst($requestType));

        if (!class_exists($requestClass)) {
            throw new \LogicException(
                sprintf('No corresponding handler class found for "%s" request type', $requestType)
            );
        }

        return $this->_objectManager->get($requestClass);
    }
}
