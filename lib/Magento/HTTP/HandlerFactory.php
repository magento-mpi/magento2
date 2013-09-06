<?php
/**
 * Request Handler factory
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */ 
class Magento_HTTP_HandlerFactory
{

    /**
     * Application object manager
     *
     * @var Magento_ObjectManager
     */
    protected $_objectManager;

    /**
     * @param Magento_ObjectManager $objectManager
     */
    public function __construct(Magento_ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Create new http request handler
     *
     * @param string $name
     * @return Magento_HTTP_HandlerInterface
     */
    public function create($name)
    {
        return $this->_objectManager->create($name);
    }
}
