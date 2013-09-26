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
class Magento_ImportExport_Model_Export_Factory
{
    /**
     * Object Manager
     *
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
     * @param string $className
     * @return Magento_Data_Collection
     * @throws InvalidArgumentException
     */
    public function create($className)
    {
        if (!$className) {
            throw new InvalidArgumentException('Incorrect class name');
        }

        $attributeCollection = $this->_objectManager->create($className);

        if (!$attributeCollection instanceof Magento_Data_Collection) {
            throw new InvalidArgumentException(sprintf(
                "Attribute factory class \"%s\" must implement Magento_Data_Collection.", get_class($attributeCollection)
            ));
        }
        return ;
    }
}
