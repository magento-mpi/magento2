<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/* @var $installer \Magento\Eav\Model\Entity\Setup */
$installer = $this;

$installer->getConnection()->insertForce(
    $installer->getTable('cataloginventory_stock'),
    array('stock_id' => 1, 'stock_name' => 'Default')
);
