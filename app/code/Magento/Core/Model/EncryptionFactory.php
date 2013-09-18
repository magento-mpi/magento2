<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Core_Model_EncryptionFactory
{
    /**
     * @var Magento_ObjectManager|null
     */
    protected $_objectManager = null;

    /**
     * @param Magento_ObjectManager $objectManager
     */
    public function __construct(Magento_ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * @param string $className
     * @param array $arguments
     * @return Magento_Core_Model_EncryptionInterface
     * @throws InvalidArgumentException
     */
    public function create($className, array $arguments = array())
    {
        $encryption = $this->_objectManager->create($className, $arguments);
        if (!$encryption instanceof Magento_Core_Model_EncryptionInterface) {
            throw new InvalidArgumentException("'{$className}' don't implement Magento_Core_Model_EncryptionInterface");
        }
        return $encryption;
    }
}
