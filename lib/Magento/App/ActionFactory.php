<?php
/**
 * Action Factory
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\App;

use Magento\App\Action\AbstractAction;

class ActionFactory
{
    /**
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
     * @param string $controllerName
     * @param array $arguments
     * @return AbstractAction
     */
    public function createController($controllerName, array $arguments = array())
    {
        $context = $this->_objectManager->create('Magento\App\Action\Context', $arguments);
        $arguments['context'] = $context;
        return $this->_objectManager->create($controllerName, $arguments);
    }
}
