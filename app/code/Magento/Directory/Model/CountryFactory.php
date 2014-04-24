<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Directory
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Country factory
 *
 * @category   Magento
 * @package    Magento_Directory
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Directory\Model;

class CountryFactory
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
     * Create new country model
     *
     * @param array $arguments
     * @return \Magento\Directory\Model\Country
     */
    public function create(array $arguments = array())
    {
        return $this->_objectManager->create('Magento\Directory\Model\Country', $arguments, false);
    }
}
