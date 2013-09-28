<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\Model;

class EncryptionFactory
{
    /**
     * @var \Magento\ObjectManager|null
     */
    protected $_objectManager = null;

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
     * @return \Magento\Core\Model\EncryptionInterface
     * @throws \InvalidArgumentException
     */
    public function create($className, array $arguments = array())
    {
        $encryption = $this->_objectManager->create($className, $arguments);
        if (!$encryption instanceof \Magento\Core\Model\EncryptionInterface) {
            throw new \InvalidArgumentException("'{$className}' don't implement \Magento\Core\Model\EncryptionInterface");
        }
        return $encryption;
    }
}
