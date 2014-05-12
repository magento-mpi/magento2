<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Region factory
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Directory\Model;

class RegionFactory
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
     * Create new region model
     *
     * @param array $arguments
     * @return \Magento\Directory\Model\Region
     */
    public function create(array $arguments = array())
    {
        return $this->_objectManager->create('Magento\Directory\Model\Region', $arguments);
    }
}
