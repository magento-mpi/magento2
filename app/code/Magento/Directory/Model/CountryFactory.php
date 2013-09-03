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
class Magento_Directory_Model_CountryFactory
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
     * Create new country model
     *
     * @param array $arguments
     * @return Magento_Directory_Model_Country
     */
    public function create(array $arguments = array())
    {
        return $this->_objectManager->create('Magento_Directory_Model_Country', $arguments, false);
    }
}
