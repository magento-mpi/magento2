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
 * Order entity resource model
 */
class Magento_Sales_Model_Resource_Report_Order extends Magento_Sales_Model_Resource_Report_Abstract
{
    /**
     * @var Magento_Sales_Model_Resource_Report_Order_CreatedatFactory
     */
    protected $_createDatFactory;

    /**
     * @var Magento_Sales_Model_Resource_Report_Order_Updatedat
     */
    protected $_updateDatFactory;

    /**
     * @param Magento_Core_Model_Resource $resource
     * @param Magento_Sales_Model_Resource_Report_Order_CreatedatFactory $createDatFactory
     * @param Magento_Sales_Model_Resource_Report_Order_Updatedat $updateDatFactory
     */
    public function __construct(
        Magento_Core_Model_Resource $resource,
        Magento_Sales_Model_Resource_Report_Order_CreatedatFactory $createDatFactory,
        Magento_Sales_Model_Resource_Report_Order_Updatedat $updateDatFactory
    ) {
        parent::__construct($resource);
        $this->_createDatFactory = $createDatFactory;
        $this->_updateDatFactory = $updateDatFactory;
    }

    /**
     * Model initialization
     */
    protected function _construct()
    {
        $this->_init('sales_order_aggregated_created', 'id');
    }

    /**
     * Aggregate Orders data
     *
     * @param mixed $from
     * @param mixed $to
     * @return Magento_Sales_Model_Resource_Report_Order
     */
    public function aggregate($from = null, $to = null)
    {
        $this->_createDatFactory->create()->aggregate($from, $to);
        $this->_updateDatFactory->create()->aggregate($from, $to);
        $this->_setFlagData(Magento_Reports_Model_Flag::REPORT_ORDER_FLAG_CODE);
        return $this;
    }
}
