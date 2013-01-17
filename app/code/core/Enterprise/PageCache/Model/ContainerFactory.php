<?php
/**
 *
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Enterprise_PageCache_Model_ContainerFactory
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
     * Create container instance
     * @param $className
     * @param array $arguments
     * @return Enterprise_PageCache_Model_ContainerInterface
     * @throws LogicException
     */
    public function create($className, array $arguments = array())
    {
        $processor = $this->_objectManager->create($className, $arguments);

        if (false === ($processor instanceof Enterprise_PageCache_Model_ContainerInterface)) {
            throw new LogicException($className . ' doesn\'t implements Enterprise_PageCache_Model_ContainerInterface');
        }

        return $processor;
    }
}
