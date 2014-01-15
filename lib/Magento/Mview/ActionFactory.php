<?php
/**
 * {license_notice}
 *   
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Mview;

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
     * Create action class instance by class name
     *
     * @param string $className
     * @throws \InvalidArgumentException
     * @return ActionInterface
     */
    public function create($className)
    {
        $action = $this->objectManager->create($className);
        if (!($action instanceof ActionInterface)) {
            throw new \InvalidArgumentException($className . ' doesn\'t implement \Magento\Mview\ActionInterface');
        }

        return $action;
    }
}
