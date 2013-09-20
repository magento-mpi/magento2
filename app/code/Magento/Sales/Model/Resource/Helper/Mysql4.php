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
 * Sales Mysql resource helper model
 */
class Magento_Sales_Model_Resource_Helper_Mysql4 extends Magento_Core_Model_Resource_Helper_Mysql4
    implements Magento_Sales_Model_Resource_Helper_Interface
{
    /**
     * @var Magento_Reports_Model_Resource_Helper_Mysql4
     */
    protected $_reportsResourceHelper;

    /**
     * @param Magento_Reports_Model_Resource_Helper_Mysql4 $reportsResourceHelper
     * @param string $modulePrefix
     */
    public function __construct(
        Magento_Reports_Model_Resource_Helper_Mysql4 $reportsResourceHelper,
        $modulePrefix = 'sales'
    ) {
        parent::__construct($modulePrefix);
        $this->_reportsResourceHelper = $reportsResourceHelper;
    }

    /**
     * Update rating position
     *
     * @param string $aggregation One of Magento_Sales_Model_Resource_Report_Bestsellers::AGGREGATION_XXX constants
     * @param array $aggregationAliases
     * @param string $mainTable
     * @param string $aggregationTable
     * @return Magento_Sales_Model_Resource_Helper_Mysql4
     */
    public function getBestsellersReportUpdateRatingPos($aggregation, $aggregationAliases,
        $mainTable, $aggregationTable
    ) {
        if ($aggregation == $aggregationAliases['monthly']) {
            $this->_reportsResourceHelper->updateReportRatingPos('month', 'qty_ordered', $mainTable, $aggregationTable);
        } elseif ($aggregation == $aggregationAliases['yearly']) {
            $this->_reportsResourceHelper->updateReportRatingPos('year', 'qty_ordered', $mainTable, $aggregationTable);
        } else {
            $this->_reportsResourceHelper->updateReportRatingPos('day', 'qty_ordered', $mainTable, $aggregationTable);
        }

        return $this;
    }
}
