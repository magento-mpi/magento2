<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Factory creating \Magento\Framework\Validator\Builder and \Magento\Framework\Validator\Validator
 *
 * @TODO Eliminate this factory in favor of strictly typified, not involving object manager with arbitrary class name
 */
namespace Magento\Framework\Validator;

class UniversalFactory
{
    /**
     * @var \Magento\Framework\ObjectManager
     */
    protected $_objectManager;

    /**
     * @param \Magento\Framework\ObjectManager $objectManager
     */
    public function __construct(\Magento\Framework\ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * @param string $className
     * @param array $arguments
     * @return \Magento\Framework\Validator\Builder
     */
    public function create($className, array $arguments = array())
    {
        return $this->_objectManager->create($className, $arguments);
    }
}
