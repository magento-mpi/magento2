<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Factory creating \Magento\Validator\Builder and \Magento\Validator\Validator
 *
 * @TODO Eliminate this factory in favor of strictly typified, not involving object manager with arbitrary class name
 */
namespace Magento\Validator;

class UniversalFactory
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
     * @param string $className
     * @param array $arguments
     * @return \Magento\Validator\Builder
     */
    public function create($className, array $arguments = array())
    {
        return $this->_objectManager->create($className, $arguments);
    }
}
