<?php
/**
 * Setup model factory
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Core\Model\Resource;

class SetupFactory
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
     * Create setup model instance
     *
     * @param $className
     * @param array $arguments
     * @return \Magento\Core\Model\Resource\SetupInterface
     * @throws \LogicException
     */
    public function create($className, array $arguments = array())
    {
        $object = $this->_objectManager->create($className, $arguments);
        if (false == ($object instanceof \Magento\Core\Model\Resource\SetupInterface)) {
            throw new \LogicException($className . ' doesn\'t implement \Magento\Core\Model\Resource\SetupInterface');
        }
        return $object;
    }
}
