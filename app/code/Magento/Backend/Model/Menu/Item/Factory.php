<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\Model\Menu\Item;

class Factory
{
    /**
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
     * Create menu item from array
     *
     * @param array $data
     * @return \Magento\Backend\Model\Menu\Item
     */
    public function create(array $data = array())
    {
        return $this->_objectManager->create('Magento\Backend\Model\Menu\Item', array('data' => $data));
    }
}
