<?php
/**
 * FPC request processor factory
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Enterprise_PageCache_Model_RequestProcessorFactory
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
     * @return Enterprise_PageCache_Model_RequestProcessorInterface
     * @throws LogicException
     */
    public function create($className, array $arguments = array())
    {
        if(false === is_subclass_of($className, 'Enterprise_PageCache_Model_RequestProcessorInterface')) {
            throw new LogicException(
                $className . ' doesn\'t implement Enterprise_PageCache_Model_RequestProcessorInterface'
            );
        }
        $processor = $this->_objectManager->create($className, $arguments);

        return $processor;
    }
}
