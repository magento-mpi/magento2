<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

$installer = $this;
/** @var $installer \Magento\Framework\Module\Setup */
$installer = $this;
$connection = $installer->getConnection();
$connection->createTable(
    $connection->createTableByDdl(
        $installer->getTable('coupon_aggregated'),
        $installer->getTable('coupon_aggregated_updated')
    )
);
