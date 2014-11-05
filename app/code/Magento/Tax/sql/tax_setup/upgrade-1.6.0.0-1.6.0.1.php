<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $installer \Magento\Setup\Module\SetupModule */
$installer = $this;
$connection = $installer->getConnection();
$connection->createTable(
    $connection->createTableByDdl(
        $installer->getTable('tax_order_aggregated_created'),
        $installer->getTable('tax_order_aggregated_updated')
    )
);
