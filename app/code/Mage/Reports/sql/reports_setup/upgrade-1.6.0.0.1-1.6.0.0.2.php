<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Reports
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $installer Magento_Core_Model_Resource_Setup */
$installer = $this;
/*
 * Rename incorrectly named tables in early magento 2 development version
 */
$installer->startSetup();

$aggregationTablesToRename = array(
    'reports_viewed_aggregated_daily'   => Mage_Reports_Model_Resource_Report_Product_Viewed::AGGREGATION_DAILY,
    'reports_viewed_aggregated_monthly' => Mage_Reports_Model_Resource_Report_Product_Viewed::AGGREGATION_MONTHLY,
    'reports_viewed_aggregated_yearly'  => Mage_Reports_Model_Resource_Report_Product_Viewed::AGGREGATION_YEARLY,
);

foreach ($aggregationTablesToRename as $wrongName => $rightName) {
    if ($installer->tableExists($wrongName)) {
        $installer->getConnection()->renameTable($installer->getTable($wrongName), $installer->getTable($rightName));
    }
}

$installer->endSetup();
