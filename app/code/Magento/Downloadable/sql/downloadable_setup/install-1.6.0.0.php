<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Downloadable
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $installer \Magento\Catalog\Model\Resource\Setup */
$installer = $this;

$installer->startSetup();

/**
 * Create table 'downloadable_link'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('downloadable_link'))
    ->addColumn('link_id', \Magento\DB\Ddl\Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Link ID')
    ->addColumn('product_id', \Magento\DB\Ddl\Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Product ID')
    ->addColumn('sort_order', \Magento\DB\Ddl\Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Sort order')
    ->addColumn('number_of_downloads', \Magento\DB\Ddl\Table::TYPE_INTEGER, null, array(
        'nullable'  => true,
        ), 'Number of downloads')
    ->addColumn('is_shareable', \Magento\DB\Ddl\Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Shareable flag')
    ->addColumn('link_url', \Magento\DB\Ddl\Table::TYPE_TEXT, 255, array(
        ), 'Link Url')
    ->addColumn('link_file', \Magento\DB\Ddl\Table::TYPE_TEXT, 255, array(
        ), 'Link File')
    ->addColumn('link_type', \Magento\DB\Ddl\Table::TYPE_TEXT, 20, array(
        ), 'Link Type')
    ->addColumn('sample_url', \Magento\DB\Ddl\Table::TYPE_TEXT, 255, array(
        ), 'Sample Url')
    ->addColumn('sample_file', \Magento\DB\Ddl\Table::TYPE_TEXT, 255, array(
        ), 'Sample File')
    ->addColumn('sample_type', \Magento\DB\Ddl\Table::TYPE_TEXT, 20, array(
        ), 'Sample Type')
    ->addIndex($installer->getIdxName('downloadable_link', 'product_id'), 'product_id')
    ->addIndex($installer->getIdxName('downloadable_link', array('product_id','sort_order')),
        array('product_id','sort_order'))
    ->addForeignKey($installer->getFkName('downloadable_link', 'product_id', 'catalog_product_entity', 'entity_id'),
        'product_id', $installer->getTable('catalog_product_entity'), 'entity_id',
        \Magento\DB\Ddl\Table::ACTION_CASCADE, \Magento\DB\Ddl\Table::ACTION_CASCADE)
    ->setComment('Downloadable Link Table');
$installer->getConnection()->createTable($table);

/**
 * Create table 'downloadable_link_price'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('downloadable_link_price'))
    ->addColumn('price_id', \Magento\DB\Ddl\Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Price ID')
    ->addColumn('link_id', \Magento\DB\Ddl\Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Link ID')
    ->addColumn('website_id', \Magento\DB\Ddl\Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Website ID')
    ->addColumn('price', \Magento\DB\Ddl\Table::TYPE_DECIMAL, '12,4', array(
        'nullable'  => false,
        'default'   => '0.0000',
        ), 'Price')
    ->addIndex($installer->getIdxName('downloadable_link_price', 'link_id'), 'link_id')
    ->addForeignKey($installer->getFkName('downloadable_link_price', 'link_id', 'downloadable_link', 'link_id'),
        'link_id', $installer->getTable('downloadable_link'), 'link_id',
        \Magento\DB\Ddl\Table::ACTION_CASCADE, \Magento\DB\Ddl\Table::ACTION_CASCADE)
    ->addIndex($installer->getIdxName('downloadable_link_price', 'website_id'), 'website_id')
    ->addForeignKey($installer->getFkName('downloadable_link_price', 'website_id', 'store_website', 'website_id'),
        'website_id', $installer->getTable('store_website'), 'website_id',
        \Magento\DB\Ddl\Table::ACTION_CASCADE, \Magento\DB\Ddl\Table::ACTION_CASCADE)
    ->setComment('Downloadable Link Price Table');
$installer->getConnection()->createTable($table);

/**
 * Create table 'downloadable_link_purchased'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('downloadable_link_purchased'))
    ->addColumn('purchased_id', \Magento\DB\Ddl\Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Purchased ID')
    ->addColumn('order_id', \Magento\DB\Ddl\Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'default'   => '0',
        ), 'Order ID')
    ->addColumn('order_increment_id', \Magento\DB\Ddl\Table::TYPE_TEXT, 50, array(
        ), 'Order Increment ID')
    ->addColumn('order_item_id', \Magento\DB\Ddl\Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Order Item ID')
    ->addColumn('created_at', \Magento\DB\Ddl\Table::TYPE_TIMESTAMP, null, array(
        'nullable'  => false,
        ), 'Date of creation')
    ->addColumn('updated_at', \Magento\DB\Ddl\Table::TYPE_TIMESTAMP, null, array(
        'nullable'  => false,
        ), 'Date of modification')
    ->addColumn('customer_id', \Magento\DB\Ddl\Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => true,
        'default'   => '0',
        ), 'Customer ID')
    ->addColumn('product_name', \Magento\DB\Ddl\Table::TYPE_TEXT, 255, array(
        ), 'Product name')
    ->addColumn('product_sku', \Magento\DB\Ddl\Table::TYPE_TEXT, 255, array(
        ), 'Product sku')
    ->addColumn('link_section_title', \Magento\DB\Ddl\Table::TYPE_TEXT, 255, array(
        ), 'Link_section_title')
    ->addIndex($installer->getIdxName('downloadable_link_purchased', 'order_id'), 'order_id')
    ->addIndex($installer->getIdxName('downloadable_link_purchased', 'order_item_id'), 'order_item_id')
    ->addIndex($installer->getIdxName('downloadable_link_purchased', 'customer_id'), 'customer_id')
    ->addForeignKey($installer->getFkName('downloadable_link_purchased', 'customer_id', 'customer_entity', 'entity_id'),
        'customer_id', $installer->getTable('customer_entity'), 'entity_id',
        \Magento\DB\Ddl\Table::ACTION_SET_NULL, \Magento\DB\Ddl\Table::ACTION_CASCADE)
    ->addForeignKey($installer->getFkName('downloadable_link_purchased', 'order_id', 'sales_flat_order', 'entity_id'),
        'order_id', $installer->getTable('sales_flat_order'), 'entity_id',
        \Magento\DB\Ddl\Table::ACTION_SET_NULL, \Magento\DB\Ddl\Table::ACTION_CASCADE)
    ->setComment('Downloadable Link Purchased Table');
$installer->getConnection()->createTable($table);

/**
 * Create table 'downloadable_link_purchased_item'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('downloadable_link_purchased_item'))
    ->addColumn('item_id', \Magento\DB\Ddl\Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Item ID')
    ->addColumn('purchased_id', \Magento\DB\Ddl\Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Purchased ID')
    ->addColumn('order_item_id', \Magento\DB\Ddl\Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'default'   => '0',
        ), 'Order Item ID')
    ->addColumn('product_id', \Magento\DB\Ddl\Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => true,
        'default'   => '0',
        ), 'Product ID')
    ->addColumn('link_hash', \Magento\DB\Ddl\Table::TYPE_TEXT, 255, array(
        ), 'Link hash')
    ->addColumn('number_of_downloads_bought', \Magento\DB\Ddl\Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Number of downloads bought')
    ->addColumn('number_of_downloads_used', \Magento\DB\Ddl\Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Number of downloads used')
    ->addColumn('link_id', \Magento\DB\Ddl\Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Link ID')
    ->addColumn('link_title', \Magento\DB\Ddl\Table::TYPE_TEXT, 255, array(
        ), 'Link Title')
    ->addColumn('is_shareable', \Magento\DB\Ddl\Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Shareable Flag')
    ->addColumn('link_url', \Magento\DB\Ddl\Table::TYPE_TEXT, 255, array(
        ), 'Link Url')
    ->addColumn('link_file', \Magento\DB\Ddl\Table::TYPE_TEXT, 255, array(
        ), 'Link File')
    ->addColumn('link_type', \Magento\DB\Ddl\Table::TYPE_TEXT, 255, array(
        ), 'Link Type')
    ->addColumn('status', \Magento\DB\Ddl\Table::TYPE_TEXT, 50, array(
        ), 'Status')
    ->addColumn('created_at', \Magento\DB\Ddl\Table::TYPE_TIMESTAMP, null, array(
        'nullable'  => false,
        ), 'Creation Time')
    ->addColumn('updated_at', \Magento\DB\Ddl\Table::TYPE_TIMESTAMP, null, array(
        'nullable'  => false,
        ), 'Update Time')
    ->addIndex($installer->getIdxName('downloadable_link_purchased_item', 'link_hash'), 'link_hash')
    ->addIndex($installer->getIdxName('downloadable_link_purchased_item', 'order_item_id'), 'order_item_id')
    ->addIndex($installer->getIdxName('downloadable_link_purchased_item', 'purchased_id'), 'purchased_id')
    ->addForeignKey(
        $installer->getFkName(
            'downloadable_link_purchased_item',
            'purchased_id',
            'downloadable_link_purchased',
            'purchased_id'
        ),
        'purchased_id', $installer->getTable('downloadable_link_purchased'), 'purchased_id',
        \Magento\DB\Ddl\Table::ACTION_CASCADE, \Magento\DB\Ddl\Table::ACTION_CASCADE)
    ->addForeignKey(
        $installer->getFkName(
            'downloadable_link_purchased_item',
            'order_item_id',
            'sales_flat_order_item',
            'item_id'
        ),
        'order_item_id', $installer->getTable('sales_flat_order_item'), 'item_id',
        \Magento\DB\Ddl\Table::ACTION_SET_NULL, \Magento\DB\Ddl\Table::ACTION_CASCADE)
    ->setComment('Downloadable Link Purchased Item Table');
$installer->getConnection()->createTable($table);

/**
 * Create table 'downloadable_link_title'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('downloadable_link_title'))
    ->addColumn('title_id', \Magento\DB\Ddl\Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Title ID')
    ->addColumn('link_id', \Magento\DB\Ddl\Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Link ID')
    ->addColumn('store_id', \Magento\DB\Ddl\Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Store ID')
    ->addColumn('title', \Magento\DB\Ddl\Table::TYPE_TEXT, 255, array(
        ), 'Title')
    ->addIndex(
        $installer->getIdxName(
            'downloadable_link_title',
            array('link_id', 'store_id'),
            \Magento\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
        ),
        array('link_id', 'store_id'),
        array('type'=>\Magento\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE))
    ->addIndex($installer->getIdxName('downloadable_link_title', 'link_id'), 'link_id')
    ->addForeignKey($installer->getFkName('downloadable_link_title', 'link_id', 'downloadable_link', 'link_id'),
        'link_id', $installer->getTable('downloadable_link'), 'link_id',
        \Magento\DB\Ddl\Table::ACTION_CASCADE, \Magento\DB\Ddl\Table::ACTION_CASCADE)
    ->addIndex($installer->getIdxName('downloadable_link_title', 'store_id'), 'store_id')
    ->addForeignKey($installer->getFkName('downloadable_link_title', 'store_id', 'store', 'store_id'),
        'store_id', $installer->getTable('store'), 'store_id',
        \Magento\DB\Ddl\Table::ACTION_CASCADE, \Magento\DB\Ddl\Table::ACTION_CASCADE)
    ->setComment('Link Title Table');
$installer->getConnection()->createTable($table);

/**
 * Create table 'downloadable_sample'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('downloadable_sample'))
    ->addColumn('sample_id', \Magento\DB\Ddl\Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Sample ID')
    ->addColumn('product_id', \Magento\DB\Ddl\Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Product ID')
    ->addColumn('sample_url', \Magento\DB\Ddl\Table::TYPE_TEXT, 255, array(
        ), 'Sample URL')
    ->addColumn('sample_file', \Magento\DB\Ddl\Table::TYPE_TEXT, 255, array(
        ), 'Sample file')
    ->addColumn('sample_type', \Magento\DB\Ddl\Table::TYPE_TEXT, 20, array(
        ), 'Sample Type')
    ->addColumn('sort_order', \Magento\DB\Ddl\Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Sort Order')
    ->addIndex($installer->getIdxName('downloadable_sample', 'product_id'), 'product_id')
    ->addForeignKey($installer->getFkName('downloadable_sample', 'product_id', 'catalog_product_entity', 'entity_id'),
        'product_id', $installer->getTable('catalog_product_entity'), 'entity_id',
        \Magento\DB\Ddl\Table::ACTION_CASCADE, \Magento\DB\Ddl\Table::ACTION_CASCADE)
    ->setComment('Downloadable Sample Table');
$installer->getConnection()->createTable($table);

/**
 * Create table 'downloadable_sample_title'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('downloadable_sample_title'))
    ->addColumn('title_id', \Magento\DB\Ddl\Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Title ID')
    ->addColumn('sample_id', \Magento\DB\Ddl\Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Sample ID')
    ->addColumn('store_id', \Magento\DB\Ddl\Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Store ID')
    ->addColumn('title', \Magento\DB\Ddl\Table::TYPE_TEXT, 255, array(
        ), 'Title')
    ->addIndex(
        $installer->getIdxName(
            'downloadable_sample_title',
            array('sample_id', 'store_id'),
            \Magento\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
        ),
        array('sample_id', 'store_id'),
        array('type'=>\Magento\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE))
    ->addIndex($installer->getIdxName('downloadable_sample_title', 'sample_id'), 'sample_id')
    ->addForeignKey($installer->getFkName('downloadable_sample_title', 'sample_id', 'downloadable_sample', 'sample_id'),
        'sample_id', $installer->getTable('downloadable_sample'), 'sample_id',
        \Magento\DB\Ddl\Table::ACTION_CASCADE, \Magento\DB\Ddl\Table::ACTION_CASCADE)
    ->addIndex($installer->getIdxName('downloadable_sample_title', 'store_id'), 'store_id')
    ->addForeignKey($installer->getFkName('downloadable_sample_title', 'store_id', 'store', 'store_id'),
        'store_id', $installer->getTable('store'), 'store_id',
        \Magento\DB\Ddl\Table::ACTION_CASCADE, \Magento\DB\Ddl\Table::ACTION_CASCADE)
    ->setComment('Downloadable Sample Title Table');
$installer->getConnection()->createTable($table);

/**
 * Create table 'catalog_product_index_price_downlod_idx'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('catalog_product_index_price_downlod_idx'))
    ->addColumn('entity_id', \Magento\DB\Ddl\Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Entity ID')
    ->addColumn('customer_group_id', \Magento\DB\Ddl\Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Customer Group ID')
    ->addColumn('website_id', \Magento\DB\Ddl\Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Website ID')
    ->addColumn('min_price', \Magento\DB\Ddl\Table::TYPE_DECIMAL, '12,4', array(
        'nullable'  => false,
        'default'   => '0.0000',
        ), 'Minimum price')
    ->addColumn('max_price', \Magento\DB\Ddl\Table::TYPE_DECIMAL, '12,4', array(
        'nullable'  => false,
        'default'   => '0.0000',
        ), 'Maximum price')
    ->setComment('Indexer Table for price of downloadable products');
$installer->getConnection()->createTable($table);

/**
 * Create table 'catalog_product_index_price_downlod_tmp'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('catalog_product_index_price_downlod_tmp'))
    ->addColumn('entity_id', \Magento\DB\Ddl\Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Entity ID')
    ->addColumn('customer_group_id', \Magento\DB\Ddl\Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Customer Group ID')
    ->addColumn('website_id', \Magento\DB\Ddl\Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Website ID')
    ->addColumn('min_price', \Magento\DB\Ddl\Table::TYPE_DECIMAL, '12,4', array(
        'nullable'  => false,
        'default'   => '0.0000',
        ), 'Minimum price')
    ->addColumn('max_price', \Magento\DB\Ddl\Table::TYPE_DECIMAL, '12,4', array(
        'nullable'  => false,
        'default'   => '0.0000',
        ), 'Maximum price')
    ->setComment('Temporary Indexer Table for price of downloadable products')
    ->setOption('type', 'MEMORY');
$installer->getConnection()->createTable($table);


/**
 * Add attributes to the eav/attribute table
 */
$installer->addAttribute(\Magento\Catalog\Model\Product::ENTITY, 'links_purchased_separately', array(
    'type'                    => 'int',
    'backend'                 => '',
    'frontend'                => '',
    'label'                   => 'Links can be purchased separately',
    'input'                   => '',
    'class'                   => '',
    'source'                  => '',
    'global'                  => \Magento\Catalog\Model\Resource\Eav\Attribute::SCOPE_GLOBAL,
    'visible'                 => false,
    'required'                => true,
    'user_defined'            => false,
    'default'                 => '',
    'searchable'              => false,
    'filterable'              => false,
    'comparable'              => false,
    'visible_on_front'        => false,
    'unique'                  => false,
    'apply_to'                => 'downloadable',
    'used_in_product_listing' => true
));

$installer->addAttribute(\Magento\Catalog\Model\Product::ENTITY, 'samples_title', array(
    'type'              => 'varchar',
    'backend'           => '',
    'frontend'          => '',
    'label'             => 'Samples title',
    'input'             => '',
    'class'             => '',
    'source'            => '',
    'global'            => \Magento\Catalog\Model\Resource\Eav\Attribute::SCOPE_STORE,
    'visible'           => false,
    'required'          => true,
    'user_defined'      => false,
    'default'           => '',
    'searchable'        => false,
    'filterable'        => false,
    'comparable'        => false,
    'visible_on_front'  => false,
    'unique'            => false,
    'apply_to'          => 'downloadable',
));

$installer->addAttribute(\Magento\Catalog\Model\Product::ENTITY, 'links_title', array(
    'type'              => 'varchar',
    'backend'           => '',
    'frontend'          => '',
    'label'             => 'Links title',
    'input'             => '',
    'class'             => '',
    'source'            => '',
    'global'            => \Magento\Catalog\Model\Resource\Eav\Attribute::SCOPE_STORE,
    'visible'           => false,
    'required'          => true,
    'user_defined'      => false,
    'default'           => '',
    'searchable'        => false,
    'filterable'        => false,
    'comparable'        => false,
    'visible_on_front'  => false,
    'unique'            => false,
    'apply_to'          => 'downloadable',
));

$installer->addAttribute(\Magento\Catalog\Model\Product::ENTITY, 'links_exist', array(
    'type'                      => 'int',
    'backend'                   => '',
    'frontend'                  => '',
    'label'                     => '',
    'input'                     => '',
    'class'                     => '',
    'source'                    => '',
    'global'                    => true,
    'visible'                   => false,
    'required'                  => false,
    'user_defined'              => false,
    'default'                   => '0',
    'searchable'                => false,
    'filterable'                => false,
    'comparable'                => false,
    'visible_on_front'          => false,
    'unique'                    => false,
    'apply_to'                  => 'downloadable',
    'used_in_product_listing'   => 1
));

$installer->endSetup();
