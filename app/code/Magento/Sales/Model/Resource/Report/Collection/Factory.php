<?php
/**
 * {license_notice}
 *   
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Sales_Model_Resource_Report_Collection_Factory
{
    /**
     * @var Magento_ObjectManager
     */
    protected $_objectManager;

    /**
     * @param Magento_ObjectManager $objectManager
     */
    public function __construct(Magento_ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * @param string $className
     * @param array $data
     * @return Magento_Sales_Model_Resource_Report_Collection_Abstract
     * @throws InvalidArgumentException
     */
    public function create($className, array $data = array())
    {
        $instance = $this->_objectManager->create($className, $data);

        if (!($instance instanceof Magento_Sales_Model_Resource_Report_Collection_Abstract)) {
            throw new InvalidArgumentException(
                $className . ' does not implement Magento_Sales_Model_Resource_Report_Collection_Abstract'
            );
        }
        return $instance;
    }
}