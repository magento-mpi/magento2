<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */

$installer = $this;
/** @var $installer \Magento\Core\Model\Resource\Setup */
$installer = $this;
$connection = $installer->getConnection();
$connection->createTable(
    $connection->createTableByDdl(
        $installer->getTable('coupon_aggregated'),
        $installer->getTable('coupon_aggregated_updated')
    )
);
