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

class Magento_FullPageCache_Model_Container_MessagesStorageFactory
{
    /**
     * @var Magento_ObjectManager
     */
    protected $_objectManager;

    /**
     * @var Magento_Object[]
     */
    protected $_storages;

    /**
     * @param Magento_ObjectManager $objectManager
     */
    public function __construct(Magento_ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Get storage model instance
     *
     * @param string $storage
     * @return Magento_Object
     * @throws InvalidArgumentException
     */
    public function get($storage)
    {
        if (!isset($this->_storages[$storage])) {
            $instance = $this->_objectManager->get($storage);
            if (!($instance instanceof Magento_Object)) {
                throw new InvalidArgumentException(
                    $storage . ' does not instance of Magento_Object'
                );
            }
            $this->_storages[$storage] = $instance;
        }
        return $this->_storages[$storage];
    }
}
