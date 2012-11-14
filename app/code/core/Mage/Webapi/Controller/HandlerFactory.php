<?php
/**
 * Factory of web API handlers.
 *
 * @copyright {}
 */
class Mage_Webapi_Controller_HandlerFactory
{
    /**
     * List of available web API handlers.
     *
     * @var array array({api type} => {API handler class})
     */
    protected $_apiTypeToHandlerMap = array(
        Mage_Webapi_Controller_Front::API_TYPE_REST => 'Mage_Webapi_Controller_Handler_Rest',
        Mage_Webapi_Controller_Front::API_TYPE_SOAP => 'Mage_Webapi_Controller_Handler_Soap',
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
    public function __construct(Magento_ObjectManager $objectManager) {
        $this->_objectManager = $objectManager;
    }

    /**
     * Create front controller instance.
     *
     * Use current API type to define proper request class.
     *
     * @param string $apiType
     * @return Mage_Webapi_Controller_HandlerAbstract
     * @throws LogicException If there is no corresponding handler class for current API type.
     */
    public function get($apiType)
    {
        if (!isset($this->_apiTypeToHandlerMap[$apiType])) {
            throw new LogicException('There is no corresponding handler class for the "%s" API type.', $apiType);
        }
        $handlerClass = $this->_apiTypeToHandlerMap[$apiType];
        return $this->_objectManager->get($handlerClass);
    }
}
