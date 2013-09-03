<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Reports_Model_Resource_Report_Collection_Factory
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
     * Create collection instance
     *
     * @param string $className
     * @param array $arguments
     * @return Magento_Core_Model_Resource_Db_Collection_Abstract
     */
    public function create($className, array $arguments = array())
    {
        return $this->_objectManager->create($className, $arguments);
    }
}