<?php
/**
 * Factory of web API dispatchers.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Webapi_Controller_Dispatcher_Factory
{
    /**
     * List of available web API dispatchers.
     *
     * @var array array({api type} => {API dispatcher class})
     */
    protected $_apiDispatcherMap = array(
        Mage_Webapi_Controller_Front::API_TYPE_REST => 'Mage_Webapi_Controller_Dispatcher_Rest',
        Mage_Webapi_Controller_Front::API_TYPE_SOAP => 'Mage_Webapi_Controller_Dispatcher_Soap',
    );

    /**
     * @var Magento_ObjectManager
     */
    protected $_objectManager;

    /**
     * Initialize dependencies.
     *
     * @param Magento_ObjectManager $objectManager
     */
    public function __construct(Magento_ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Create front controller instance.
     *
     * Use current API type to define proper request class.
     *
     * @param string $apiType
     * @return Mage_Webapi_Controller_DispatcherInterface
     * @throws LogicException If there is no corresponding dispatcher class for current API type.
     */
    public function get($apiType)
    {
        if (!isset($this->_apiDispatcherMap[$apiType])) {
            throw new LogicException(
                sprintf('There is no corresponding dispatcher class for the "%s" API type.', $apiType)
            );
        }
        $dispatcherClass = $this->_apiDispatcherMap[$apiType];
        return $this->_objectManager->get($dispatcherClass);
    }
}
