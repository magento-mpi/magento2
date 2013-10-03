<?php
/**
 * Request Handler factory
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */ 
namespace Magento\HTTP;

class HandlerFactory
{

    /**
     * Application object manager
     *
     * @var \Magento\ObjectManager
     */
    protected $_objectManager;

    /**
     * @param \Magento\ObjectManager $objectManager
     */
    public function __construct(\Magento\ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Create new http request handler
     *
     * @param string $name
     * @return \Magento\HTTP\HandlerInterface
     */
    public function create($name)
    {
        return $this->_objectManager->create($name);
    }
}
