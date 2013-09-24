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
 *
 * @category    Magento
 * @package     Magento_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Sales_Model_Resource_Helper extends Magento_Core_Model_Resource_Helper
    implements Magento_Sales_Model_Resource_Helper_Interface
{
    /**
     * Update rating position
     *
     * @param string $aggregation One of Magento_Sales_Model_Resource_Report_Bestsellers::AGGREGATION_XXX constants
     * @param array $aggregationAliases
     * @param string $mainTable
     * @param string $aggregationTable
     * @return Magento_Sales_Model_Resource_Helper
     */
    public function getBestsellersReportUpdateRatingPos($aggregation, $aggregationAliases,
        $mainTable, $aggregationTable
    ) {
        /** @var $reportsHelper Magento_Reports_Model_Resource_Helper_Interface */
        $reportsHelper = Mage::getResourceHelper('Magento_Reports');

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
