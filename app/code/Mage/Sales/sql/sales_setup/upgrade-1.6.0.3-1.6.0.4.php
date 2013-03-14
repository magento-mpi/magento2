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
 * Create aggregation tables for updated_at fields
 */

/** @var $installer Mage_Sales_Model_Resource_Setup */
$installer = $this;
$connection = $installer->getConnection();
$connection->createTable($connection->createTableByDdl(
    $installer->getTable('sales_order_aggregated_created'),
    $installer->getTable('sales_order_aggregated_updated')
));
