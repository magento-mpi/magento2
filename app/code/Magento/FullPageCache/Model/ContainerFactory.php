<?php
/**
 * FPC container factory
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\FullPageCache\Model;

class ContainerFactory
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
     * Create container instance
     *
     * @param string $className
     * @param array $arguments
     * @return \Magento\FullPageCache\Model\ContainerInterface
     *
     * @throws \LogicException
     */
    public function create($className, array $arguments = array())
    {
        $processor = $this->_objectManager->create($className, $arguments);

        if (false === ($processor instanceof \Magento\FullPageCache\Model\ContainerInterface)) {
            throw new \LogicException($className . ' doesn\'t implement \Magento\FullPageCache\Model\ContainerInterface');
        }

        return $processor;
    }
}
