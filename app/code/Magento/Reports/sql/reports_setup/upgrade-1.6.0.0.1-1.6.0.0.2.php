<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $installer Magento\Setup\Module\SetupModule */
$installer = $this;
/*
 * Rename incorrectly named tables in early magento 2 development version
 */
$installer->startSetup();

$aggregationTablesToRename = array(
    'reports_viewed_aggregated_daily' => 'report_viewed_product_aggregated_daily',
    'reports_viewed_aggregated_monthly' => 'report_viewed_product_aggregated_monthly',
    'reports_viewed_aggregated_yearly' => 'report_viewed_product_aggregated_yearly'
);

foreach ($aggregationTablesToRename as $wrongName => $rightName) {
    if ($installer->tableExists($wrongName)) {
        $installer->getConnection()->renameTable($installer->getTable($wrongName), $installer->getTable($rightName));
    }
}

$installer->endSetup();
