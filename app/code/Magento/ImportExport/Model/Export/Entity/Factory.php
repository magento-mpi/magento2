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
 * Export entity factory
 */
class Magento_ImportExport_Model_Export_Entity_Factory
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
     * @return Magento_ImportExport_Model_Export_Entity_Abstract|Magento_ImportExport_Model_Export_EntityAbstract
     * @throws InvalidArgumentException
     */
    public function create($className)
    {
        if (!$className) {
            throw new InvalidArgumentException('Incorrect class name');
        }

        return $this->_objectManager->create($className);
    }
}
