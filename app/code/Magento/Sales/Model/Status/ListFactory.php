<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Model\Status;

class ListFactory
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
     * Create status list instance
     *
     * @param array $arguments
     * @return \Magento\Sales\Model\Status\ListStatus
     */
    public function create(array $arguments = array())
    {
        return $this->_objectManager->create('Magento\Sales\Model\Status\ListStatus', $arguments);
    }
}
