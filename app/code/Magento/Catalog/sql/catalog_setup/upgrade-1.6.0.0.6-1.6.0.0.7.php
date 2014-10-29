<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

/** @var $installer \Magento\Setup\Module\SetupModule */
$installer = $this;

$installer->getConnection()->addColumn(
    $installer->getTable('catalog_category_anc_products_index_tmp'),
    'position',
    array(
        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
        'unsigned' => true,
        'nullable' => true,
        'comment' => 'Position'
    )
);
