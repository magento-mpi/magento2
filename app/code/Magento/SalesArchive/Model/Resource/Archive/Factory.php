<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_SalesArchive
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\SalesArchive\Model\Resource\Archive;

/**
 * Archive resource factory
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
     * @return \Magento\Framework\Model\Resource\Db\AbstractDb
     * @throws \InvalidArgumentException
     */
    public function get($className)
    {
        if (!$className) {
            throw new \InvalidArgumentException('Incorrect resource class name');
        }

        return $this->_objectManager->get($className);
    }
}
