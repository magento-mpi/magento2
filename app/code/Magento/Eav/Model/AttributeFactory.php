<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Eav
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * EAV attribute model factory
 *
 * @category   Magento
 * @package    Magento_Eav
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Eav_Model_AttributeFactory
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
     * create new Eav attribute instance
     *
     * @param string $className
     * @param array $arguments
     * @return mixed
     */
    public function createAttribute($className, $arguments = array())
    {
        return $this->_objectManager->create($className, array('data' => $arguments));
    }
}
