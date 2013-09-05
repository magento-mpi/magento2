<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Rma
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $installer Magento_Rma_Model_Resource_Setup */
$installer = $this;

$tableName = $installer->getTable('magento_rma_item_entity');

$installer->getConnection()
    ->addColumn(
        $tableName,
        'product_admin_name',
        array(
            'TYPE' => \Magento\DB\Ddl\Table::TYPE_TEXT,
            'LENGTH' => 255,
            'COMMENT' => 'Product Name For Backend',
        )
    );
$installer->getConnection()
    ->addColumn(
        $tableName,
        'product_admin_sku',
        array(
            'TYPE' => \Magento\DB\Ddl\Table::TYPE_TEXT,
            'LENGTH' => 255,
            'COMMENT' => 'Product Sku For Backend',
        )
    );
$installer->getConnection()
    ->addColumn(
        $tableName,
        'product_options',
        array(
            'TYPE' => \Magento\DB\Ddl\Table::TYPE_TEXT,
            'COMMENT' => 'Product Options',
        )
    );

$installer->addAttribute('rma_item', 'product_admin_name',
        array(
            'type'               => 'static',
            'label'              => 'Product Name For Backend',
            'input'              => 'text',
            'visible'            => false,
            'sort_order'         => 46,
            'position'           => 46,
        ));
$installer->addAttribute('rma_item', 'product_admin_sku',
        array(
            'type'               => 'static',
            'label'              => 'Product Sku For Backend',
            'input'              => 'text',
            'visible'            => false,
            'sort_order'         => 47,
            'position'           => 47,
        ));
$installer->addAttribute('rma_item', 'product_options',
        array(
            'type'               => 'static',
            'label'              => 'Product Options',
            'input'              => 'text',
            'visible'            => false,
            'sort_order'         => 48,
            'position'           => 48,
        ));
