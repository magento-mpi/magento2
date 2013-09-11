<?php
/**
 * Factory of web API dispatchers.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webapi\Controller\Dispatcher;

class Factory
{
    /**
     * List of available web API dispatchers.
     *
     * @var array array({api type} => {API dispatcher class})
     */
    protected $_apiDispatcherMap = array(
        \Magento\Webapi\Controller\Front::API_TYPE_REST => '\Magento\Webapi\Controller\Dispatcher\Rest',
        \Magento\Webapi\Controller\Front::API_TYPE_SOAP => '\Magento\Webapi\Controller\Dispatcher\Soap',
    );

    /**
     * @var \Magento\ObjectManager
     */
    protected $_objectManager;

    /**
     * Initialize dependencies.
     *
     * @param \Magento\ObjectManager $objectManager
     */
    public function __construct(\Magento\ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Create front controller instance.
     *
     * Use current API type to define proper request class.
     *
     * @param string $apiType
     * @return \Magento\Webapi\Controller\DispatcherInterface
     * @throws \LogicException If there is no corresponding dispatcher class for current API type.
     */
    public function get($apiType)
    {
        if (!isset($this->_apiDispatcherMap[$apiType])) {
            throw new \LogicException(
                sprintf('There is no corresponding dispatcher class for the "%s" API type.', $apiType)
            );
        }
        $dispatcherClass = $this->_apiDispatcherMap[$apiType];
        return $this->_objectManager->get($dispatcherClass);
    }
}
