<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Reports
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $installer \Magento\Core\Model\Resource\Setup */
$installer = $this;
/*
 * Rename incorrectly named tables in early magento 2 development version
 */
$installer->startSetup();

$aggregationTablesToRename = array(
    'reports_viewed_aggregated_daily'   => \Magento\Reports\Model\Resource\Report\Product\Viewed::AGGREGATION_DAILY,
    'reports_viewed_aggregated_monthly' => \Magento\Reports\Model\Resource\Report\Product\Viewed::AGGREGATION_MONTHLY,
    'reports_viewed_aggregated_yearly'  => \Magento\Reports\Model\Resource\Report\Product\Viewed::AGGREGATION_YEARLY,
);

foreach ($aggregationTablesToRename as $wrongName => $rightName) {
    if ($installer->tableExists($wrongName)) {
        $installer->getConnection()->renameTable($installer->getTable($wrongName), $installer->getTable($rightName));
    }
}

$installer->endSetup();
