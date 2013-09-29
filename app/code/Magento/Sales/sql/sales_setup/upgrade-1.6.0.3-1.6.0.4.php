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
 * Create aggregation tables for updated_at fields
 */

/** @var $installer \Magento\Sales\Model\Resource\Setup */
$installer = $this;
$connection = $installer->getConnection();
$connection->createTable($connection->createTableByDdl(
    $installer->getTable('sales_order_aggregated_created'),
    $installer->getTable('sales_order_aggregated_updated')
));
