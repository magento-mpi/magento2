<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Website factory
 */
namespace Magento\Store\Model\Website;

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
     * @param array $data
     * @return \Magento\Store\Model\Website
     */
    public function create(array $data = array())
    {
        return $this->_objectManager->create('Magento\Store\Model\Website', $data);
    }
}
