<?php
/**
 * FPC request processor factory
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\FullPageCache\Model;

class RequestProcessorFactory
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
     * Get request processor instance
     *
     * @param string $className
     * @param array $arguments
     * @return \Magento\FullPageCache\Model\RequestProcessorInterface
     * @throws \LogicException
     */
    public function create($className, array $arguments = array())
    {
        $processor = $this->_objectManager->create($className, $arguments);

        if (false === ($processor instanceof \Magento\FullPageCache\Model\RequestProcessorInterface)) {
            throw new \LogicException(
                $className . ' doesn\'t implement \Magento\FullPageCache\Model\RequestProcessorInterface'
            );
        }

        return $processor;
    }
}
