<?php
/**
 * FPC request processor factory
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_FullPageCache_Model_RequestProcessorFactory
{
    /**
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
     * Get request processor instance
     *
     * @param string $className
     * @param array $arguments
     * @return Magento_FullPageCache_Model_RequestProcessorInterface
     * @throws LogicException
     */
    public function create($className, array $arguments = array())
    {
        $processor = $this->_objectManager->create($className, $arguments);

        if (false === ($processor instanceof Magento_FullPageCache_Model_RequestProcessorInterface)) {
            throw new LogicException(
                $className . ' doesn\'t implement Magento_FullPageCache_Model_RequestProcessorInterface'
            );
        }

        return $processor;
    }
}
