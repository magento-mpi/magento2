<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */

$installer = $this;
/** @var $installer Mage_Tax_Model_Resource_Setup */
$installer = $this;
$connection = $installer->getConnection();
$connection->createTable($connection->createTableByDdl(
    $installer->getTable('tax_order_aggregated_created'),
    $installer->getTable('tax_order_aggregated_updated')
));
