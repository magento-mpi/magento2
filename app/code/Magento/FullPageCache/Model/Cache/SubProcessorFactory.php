<?php
/**
 * FPC sub-processor factory
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\FullPageCache\Model\Cache;

class SubProcessorFactory
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
     * Get subprocessor instance
     *
     * @param string $className
     * @param array $arguments
     * @return \Magento\FullPageCache\Model\Cache\SubProcessorInterface
     * @throws \LogicException
     */
    public function create($className, array $arguments = array())
    {
        $processor = $this->_objectManager->create($className, $arguments);

        if (false === ($processor instanceof \Magento\FullPageCache\Model\Cache\SubProcessorInterface)) {
            throw new \LogicException(
                $className . ' doesn\'t implements \Magento\FullPageCache\Model\Cache\SubProcessorInterface'
            );
        }

        return $processor;
    }

}
