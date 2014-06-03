<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\ImportExport\Model\Import\Entity\Product\Type;

/**
 * Import product type factory
 */
class Factory
{
    /**
     * Object Manager
     *
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
     * @return \Magento\ImportExport\Model\Import\Entity\Product\Type\AbstractType
     * @throws \InvalidArgumentException
     */
    public function create($className, array $arguments = array())
    {
        if (!$className) {
            throw new \InvalidArgumentException('Incorrect class name');
        }

        return $this->_objectManager->create($className, $arguments);
    }
}
