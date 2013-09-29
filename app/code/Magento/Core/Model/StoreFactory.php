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

class StoreFactory
{
    /**
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
     * Create store instance
     *
     * @param array $arguments
     * @return \Magento\Core\Model\Store
     */
    public function create(array $arguments = array())
    {
        return $this->_objectManager->create('Magento\Core\Model\Store', $arguments);
    }
}
