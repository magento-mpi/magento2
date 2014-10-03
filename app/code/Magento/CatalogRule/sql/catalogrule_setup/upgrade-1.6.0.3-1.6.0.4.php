<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/* @var $installer \Magento\Framework\Module\Setup */
$installer = $this;

$installer->startSetup();

$table = $installer->getTable('catalogrule_product_price');
$installer->getConnection()->dropForeignKey(
    $table,
    $installer->getFkName('catalogrule_product_price', 'product_id', 'catalog_product_entity', 'entity_id')
)->dropForeignKey(
    $table,
    $installer->getFkName('catalogrule_product_price', 'customer_group_id', 'customer_group', 'customer_group_id')
)->dropForeignKey(
    $table,
    $installer->getFkName('catalogrule_product_price', 'website_id', 'store_website', 'website_id')
);

$installer->endSetup();
