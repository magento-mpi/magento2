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
 * Report order collection
 *
 * @category    Magento
 * @package     Magento_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Sales_Model_Resource_Report_Order_Collection extends Magento_Sales_Model_Resource_Report_Collection_Abstract
{
    /**
     * Period format
     *
     * @var string
     */
    protected $_periodFormat;

    /**
     * Aggregated Data Table
     *
     * @var string
     */
    protected $_aggregationTable = 'sales_order_aggregated_created';

    /**
     * Selected columns
     *
     * @var array
     */
    protected $_selectedColumns    = array();

    /**
     * Initialize custom resource model
     *
     */
    public function __construct(
        Magento_Data_Collection_Db_FetchStrategyInterface $fetchStrategy,
        Magento_Sales_Model_Resource_Report $resource
    ) {
        $resource->init($this->_aggregationTable);
        parent::__construct($fetchStrategy, $resource);
    }

    /**
     * Get selected columns
     *
     * @return array
     */
    protected function _getSelectedColumns()
    {
        $adapter = $this->getConnection();
        if ('month' == $this->_period) {
            $this->_periodFormat = $adapter->getDateFormatSql('period', '%Y-%m');
        } elseif ('year' == $this->_period) {
            $this->_periodFormat = $adapter->getDateExtractSql('period', Magento_DB_Adapter_Interface::INTERVAL_YEAR);
        } else {
            $this->_periodFormat = $adapter->getDateFormatSql('period', '%Y-%m-%d');
        }

        if (!$this->isTotals()) {
            $this->_selectedColumns = array(
                'period'                         => $this->_periodFormat,
                'orders_count'                   => 'SUM(orders_count)',
                'total_qty_ordered'              => 'SUM(total_qty_ordered)',
                'total_qty_invoiced'             => 'SUM(total_qty_invoiced)',
                'total_income_amount'            => 'SUM(total_income_amount)',
                'total_revenue_amount'           => 'SUM(total_revenue_amount)',
                'total_profit_amount'            => 'SUM(total_profit_amount)',
                'total_invoiced_amount'          => 'SUM(total_invoiced_amount)',
                'total_canceled_amount'          => 'SUM(total_canceled_amount)',
                'total_paid_amount'              => 'SUM(total_paid_amount)',
                'total_refunded_amount'          => 'SUM(total_refunded_amount)',
                'total_tax_amount'               => 'SUM(total_tax_amount)',
                'total_tax_amount_actual'        => 'SUM(total_tax_amount_actual)',
                'total_shipping_amount'          => 'SUM(total_shipping_amount)',
                'total_shipping_amount_actual'   => 'SUM(total_shipping_amount_actual)',
                'total_discount_amount'          => 'SUM(total_discount_amount)',
                'total_discount_amount_actual'   => 'SUM(total_discount_amount_actual)',
            );
        }

        if ($this->isTotals()) {
            $this->_selectedColumns = $this->getAggregatedColumns();
        }

        return $this->_selectedColumns;
    }

    /**
     * Add selected data
     *
     * @return Magento_Sales_Model_Resource_Report_Order_Collection
     */
    protected function _initSelect()
    {
        $this->getSelect()->from($this->getResource()->getMainTable(), $this->_getSelectedColumns());
        if (!$this->isTotals()) {
            $this->getSelect()->group($this->_periodFormat);
        }
        return parent::_initSelect();
    }
}
