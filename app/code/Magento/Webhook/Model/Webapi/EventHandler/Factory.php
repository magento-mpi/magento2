<?php
/**
 * Factory for \Magento\Webhook\Model\Webapi\EventHandler objects
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Webhook
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webhook\Model\Webapi\EventHandler;

class Factory
{
    /**
     * @var \Magento\ObjectManager
     */
    protected $_objectManager;

    /**
     * Initialize the class
     *
     * @param \Magento\ObjectManager $objectManager
     */
    public function __construct(\Magento\ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Create a new instance of \Magento\Webhook\Model\Webapi\EventHandler
     *
     * @param array $arguments Fed into constructor
     * @return \Magento\Webhook\Model\Webapi\EventHandler
     */
    public function create(array $arguments = array())
    {
        return $this->_objectManager->create('Magento\Webhook\Model\Webapi\EventHandler', $arguments);
    }
}
