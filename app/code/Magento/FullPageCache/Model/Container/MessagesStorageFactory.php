<?php
/**
 * Messages storage factory
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_FullPageCache
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\FullPageCache\Model\Container;

class MessagesStorageFactory
{
    /**
     * @var \Magento\ObjectManager
     */
    protected $_objectManager;

    /**
     * @var \Magento\Object[]
     */
    protected $_storages;

    /**
     * @param \Magento\ObjectManager $objectManager
     */
    public function __construct(\Magento\ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Get storage model instance
     *
     * @param string $storage
     * @return \Magento\Object
     * @throws \InvalidArgumentException
     */
    public function get($storage)
    {
        if (!isset($this->_storages[$storage])) {
            $instance = $this->_objectManager->get($storage);
            if (!($instance instanceof \Magento\Object)) {
                throw new \InvalidArgumentException(
                    $storage . ' does not instance of \Magento\Object'
                );
            }
            $this->_storages[$storage] = $instance;
        }
        return $this->_storages[$storage];
    }
}
