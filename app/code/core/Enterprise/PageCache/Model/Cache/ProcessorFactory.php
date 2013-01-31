<?php
/**
 * FPC processor factory
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Enterprise_PageCache_Model_Cache_ProcessorFactory
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
     * Get processor instance
     *
     * @param string $className
     * @param array $arguments
     * @return Enterprise_PageCache_Model_Cache_ProcessorInterface
     * @throws LogicException
     */
    public function create($className, array $arguments = array())
    {
        $processor = $this->_objectManager->create($className, $arguments);

        if (false === ($processor instanceof Enterprise_PageCache_Model_Cache_ProcessorInterface)) {
            throw new LogicException(
                $className . ' doesn\'t implement Enterprise_PageCache_Model_Cache_ProcessorInterface'
            );
        }

        return $processor;
    }
}
