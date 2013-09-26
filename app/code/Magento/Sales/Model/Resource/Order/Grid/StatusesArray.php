<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Sales orders statuses option array
 */
class Magento_Sales_Model_Resource_Order_Grid_StatusesArray implements Magento_Core_Model_Option_ArrayInterface
{
    /**
     * @var Magento_Sales_Model_Resource_Order_Status_CollectionFactory
     */
    protected $_statusCollFactory;

    /**
     * @param Magento_Sales_Model_Resource_Order_Status_CollectionFactory $statusCollFactory
     */
    public function __construct(Magento_Sales_Model_Resource_Order_Status_CollectionFactory $statusCollFactory)
    {
        $this->_statusCollFactory = $statusCollFactory;
    }

    /**
     * Return option array
     *
     * @return array
     */
    public function toOptionArray()
    {
        $statuses = $this->_statusCollFactory->create()->toOptionHash();
        return $statuses;
    }
}
