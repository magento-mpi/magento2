<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $installer \Magento\Catalog\Model\Resource\Setup */
$installer = $this;
$connection = $installer->getConnection();

$priceIndexerTables = array('catalog_product_index_price_bundle_idx', 'catalog_product_index_price_bundle_tmp');

$optionsPriceIndexerTables = array(
    'catalog_product_index_price_bundle_opt_idx',
    'catalog_product_index_price_bundle_opt_tmp'
);

$selectionPriceIndexerTables = array(
    'catalog_product_index_price_bundle_sel_idx',
    'catalog_product_index_price_bundle_sel_tmp'
);

foreach ($priceIndexerTables as $table) {
    $connection->addColumn(
        $installer->getTable($table),
        'group_price',
        array('type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL, 'length' => '12,4', 'comment' => 'Group price')
    );
    $connection->addColumn(
        $installer->getTable($table),
        'base_group_price',
        array('type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL, 'length' => '12,4', 'comment' => 'Base Group Price')
    );
    $connection->addColumn(
        $installer->getTable($table),
        'group_price_percent',
        array('type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL, 'length' => '12,4', 'comment' => 'Group Price Percent')
    );
}

foreach (array_merge($optionsPriceIndexerTables, $selectionPriceIndexerTables) as $table) {
    $connection->addColumn(
        $installer->getTable($table),
        'group_price',
        array('type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL, 'length' => '12,4', 'comment' => 'Group price')
    );
}

foreach ($optionsPriceIndexerTables as $table) {
    $connection->addColumn(
        $installer->getTable($table),
        'alt_group_price',
        array('type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL, 'length' => '12,4', 'comment' => 'Alt Group Price')
    );
}

$applyTo = explode(',', $installer->getAttribute(\Magento\Catalog\Model\Product::ENTITY, 'group_price', 'apply_to'));
if (!in_array('bundle', $applyTo)) {
    $applyTo[] = 'bundle';
    $installer->updateAttribute(
        \Magento\Catalog\Model\Product::ENTITY,
        'group_price',
        'apply_to',
        implode(',', $applyTo)
    );
}
