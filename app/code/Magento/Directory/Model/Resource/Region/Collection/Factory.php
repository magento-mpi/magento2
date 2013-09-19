<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Factory for region resource collections
 */
namespace Magento\Directory\Model\Resource\Region\Collection;

class Factory
{
    /**
     * Object Manager
     *
     * @var \Magento\ObjectManager
     */
    protected $_objectManager;

    public function __construct(\Magento\ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * @param array $data
     * @return \Magento\Directory\Model\Resource\Region\Collection
     */
    public function create(array $data = array())
    {
        return $this->_objectManager->create('Magento\Directory\Model\Resource\Region\Collection', $data);
    }
}
