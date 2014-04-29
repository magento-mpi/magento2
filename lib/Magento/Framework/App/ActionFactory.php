<?php
/**
 * Action Factory
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\App;

use Magento\Framework\App\Action\AbstractAction;

class ActionFactory
{
    /**
     * @var \Magento\Framework\ObjectManager
     */
    protected $_objectManager;

    /**
     * @param \Magento\Framework\ObjectManager $objectManager
     */
    public function __construct(\Magento\Framework\ObjectManager $objectManager)
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
        $context = $this->_objectManager->create('Magento\Framework\App\Action\Context', $arguments);
        $arguments['context'] = $context;
        return $this->_objectManager->create($controllerName, $arguments);
    }
}
