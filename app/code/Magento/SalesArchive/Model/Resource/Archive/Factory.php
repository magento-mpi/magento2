<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_SalesArchive
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Archive resource factory
 */
class Magento_SalesArchive_Model_Resource_Archive_Factory
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
     * @return Magento_Core_Model_Resource_Db_Abstract
     * @throws InvalidArgumentException
     */
    public function get($className)
    {
        if (!$className) {
            throw new InvalidArgumentException('Incorrect resource class name');
        }

        return $this->_objectManager->get($className);
    }
}
