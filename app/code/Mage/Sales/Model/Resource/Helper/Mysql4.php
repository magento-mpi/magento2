<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Sales Mysql resource helper model
 *
 * @category    Mage
 * @package     Mage_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Sales_Model_Resource_Helper_Mysql4 extends Magento_Core_Model_Resource_Helper_Mysql4
    implements Mage_Sales_Model_Resource_Helper_Interface
{
    /**
     * Update rating position
     *
     * @param string $aggregation One of Mage_Sales_Model_Resource_Report_Bestsellers::AGGREGATION_XXX constants
     * @param array $aggregationAliases
     * @param string $mainTable
     * @param string $aggregationTable
     * @return Mage_Sales_Model_Resource_Helper_Mysql4
     */
    public function getBestsellersReportUpdateRatingPos($aggregation, $aggregationAliases,
        $mainTable, $aggregationTable
    ) {
        /** @var $reportsHelper Mage_Reports_Model_Resource_Helper_Interface */
        $reportsHelper = Mage::getResourceHelper('Mage_Reports');

        if ($aggregation == $aggregationAliases['monthly']) {
            $reportsHelper->updateReportRatingPos('month', 'qty_ordered', $mainTable, $aggregationTable);
        } elseif ($aggregation == $aggregationAliases['yearly']) {
            $reportsHelper->updateReportRatingPos('year', 'qty_ordered', $mainTable, $aggregationTable);
        } else {
            $reportsHelper->updateReportRatingPos('day', 'qty_ordered', $mainTable, $aggregationTable);
        }

        return $this;
    }
}
