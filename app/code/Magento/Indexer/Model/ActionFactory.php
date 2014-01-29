<?php
/**
 * {license_notice}
 *   
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Indexer\Model;

class ActionFactory
{
    /**
     * @var \Magento\ObjectManager
     */
    protected $objectManager;

    /**
     * @param \Magento\ObjectManager $objectManager
     */
    public function __construct(\Magento\ObjectManager $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * Get action class instance by class name
     *
     * @param string $className
     * @throws \InvalidArgumentException
     * @return ActionInterface
     */
    public function get($className)
    {
        $action = $this->objectManager->get($className);
        if (!($action instanceof ActionInterface)) {
            throw new \InvalidArgumentException(
                $className . ' doesn\'t implement \Magento\Indexer\Model\ActionInterface'
            );
        }

        return $action;
    }
}
