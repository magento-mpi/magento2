<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_ImportExport
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Entity factory
 */
namespace Magento\ImportExport\Model\Export;

class Factory
{
    /**
     * Object Manager
     *
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
     * @return \Magento\Data\Collection
     * @throws \InvalidArgumentException
     */
    public function create($className)
    {
        if (!$className) {
            throw new \InvalidArgumentException('Incorrect class name');
        }

        $attributeCollection = $this->_objectManager->create($className);

        if (!$attributeCollection instanceof \Magento\Data\Collection) {
            throw new \InvalidArgumentException(sprintf(
                "Attribute factory class \"%s\" must implement \Magento\Data\Collection.", get_class($attributeCollection)
            ));
        }
        return $attributeCollection;
    }
}
