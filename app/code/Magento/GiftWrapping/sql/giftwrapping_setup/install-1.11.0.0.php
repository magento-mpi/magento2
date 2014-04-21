<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftWrapping
 * @copyright   {copyright}
 * @license     {license_link}
 */

/* @var $this \Magento\GiftWrapping\Model\Resource\Setup */
$installer = $this;

/**
 * Create table 'magento_giftwrapping'
 */
$table = $installer->getConnection()->newTable(
    $installer->getTable('magento_giftwrapping')
)->addColumn(
    'wrapping_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
    null,
    array('identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true),
    'Wrapping Id'
)->addColumn(
    'status',
    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
    null,
    array('unsigned' => true, 'nullable' => false),
    'Status'
)->addColumn(
    'base_price',
    \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
    '12,4',
    array('nullable' => false),
    'Base Price'
)->addColumn(
    'image',
    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
    255,
    array(),
    'Image'
)->addIndex(
    $installer->getIdxName('magento_giftwrapping', array('status')),
    array('status')
)->setComment(
    'Enterprise Gift Wrapping Table'
);
$installer->getConnection()->createTable($table);


/**
 * Create table 'magento_giftwrapping_store_attributes'
 */
$table = $installer->getConnection()->newTable(
    $installer->getTable('magento_giftwrapping_store_attributes')
)->addColumn(
    'wrapping_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
    null,
    array('unsigned' => true, 'nullable' => false, 'primary' => true),
    'Wrapping Id'
)->addColumn(
    'store_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
    null,
    array('unsigned' => true, 'nullable' => false, 'primary' => true),
    'Store Id'
)->addColumn(
    'design',
    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
    255,
    array('nullable' => false),
    'Design'
)->addIndex(
    $installer->getIdxName('magento_giftwrapping_store_attributes', array('store_id')),
    array('store_id')
)->addForeignKey(
    $installer->getFkName(
        'magento_giftwrapping_store_attributes',
        'wrapping_id',
        'magento_giftwrapping',
        'wrapping_id'
    ),
    'wrapping_id',
    $installer->getTable('magento_giftwrapping'),
    'wrapping_id',
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
)->addForeignKey(
    $installer->getFkName('magento_giftwrapping_store_attributes', 'store_id', 'store', 'store_id'),
    'store_id',
    $installer->getTable('store'),
    'store_id',
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
)->setComment(
    'Enterprise Gift Wrapping Attribute Table'
);
$installer->getConnection()->createTable($table);


/**
 * Create table 'magento_giftwrapping_website'
 */
$table = $installer->getConnection()->newTable(
    $installer->getTable('magento_giftwrapping_website')
)->addColumn(
    'wrapping_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
    null,
    array('unsigned' => true, 'nullable' => false, 'primary' => true),
    'Wrapping Id'
)->addColumn(
    'website_id',
    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
    null,
    array('unsigned' => true, 'nullable' => false, 'primary' => true),
    'Website Id'
)->addIndex(
    $installer->getIdxName('magento_giftwrapping_website', array('website_id')),
    array('website_id')
)->addForeignKey(
    $installer->getFkName('magento_giftwrapping_website', 'wrapping_id', 'magento_giftwrapping', 'wrapping_id'),
    'wrapping_id',
    $installer->getTable('magento_giftwrapping'),
    'wrapping_id',
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
)->addForeignKey(
    $installer->getFkName('magento_giftwrapping_website', 'website_id', 'store_website', 'website_id'),
    'website_id',
    $installer->getTable('store_website'),
    'website_id',
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
)->setComment(
    'Enterprise Gift Wrapping Website Table'
);
$installer->getConnection()->createTable($table);

/**
 * Add gift wrapping attributes for sales entities
 */
$entityAttributesCodes = array(
    'gw_id' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
    'gw_allow_gift_receipt' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
    'gw_add_card' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
    'gw_base_price' => 'decimal',
    'gw_price' => 'decimal',
    'gw_items_base_price' => 'decimal',
    'gw_items_price' => 'decimal',
    'gw_card_base_price' => 'decimal',
    'gw_card_price' => 'decimal',
    'gw_base_tax_amount' => 'decimal',
    'gw_tax_amount' => 'decimal',
    'gw_items_base_tax_amount' => 'decimal',
    'gw_items_tax_amount' => 'decimal',
    'gw_card_base_tax_amount' => 'decimal',
    'gw_card_tax_amount' => 'decimal'
);
foreach ($entityAttributesCodes as $code => $type) {
    $installer->addAttribute('quote', $code, array('type' => $type, 'visible' => false));
    $installer->addAttribute('quote_address', $code, array('type' => $type, 'visible' => false));
    $installer->addAttribute('order', $code, array('type' => $type, 'visible' => false));
}

$itemsAttributesCodes = array(
    'gw_id' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
    'gw_base_price' => 'decimal',
    'gw_price' => 'decimal',
    'gw_base_tax_amount' => 'decimal',
    'gw_tax_amount' => 'decimal'
);
foreach ($itemsAttributesCodes as $code => $type) {
    $installer->addAttribute('quote_item', $code, array('type' => $type, 'visible' => false));
    $installer->addAttribute('quote_address_item', $code, array('type' => $type, 'visible' => false));
    $installer->addAttribute('order_item', $code, array('type' => $type, 'visible' => false));
}

$entityAttributesCodes = array(
    'gw_base_price_invoiced' => 'decimal',
    'gw_price_invoiced' => 'decimal',
    'gw_items_base_price_invoiced' => 'decimal',
    'gw_items_price_invoiced' => 'decimal',
    'gw_card_base_price_invoiced' => 'decimal',
    'gw_card_price_invoiced' => 'decimal',
    'gw_base_tax_amount_invoiced' => 'decimal',
    'gw_tax_amount_invoiced' => 'decimal',
    'gw_items_base_tax_invoiced' => 'decimal',
    'gw_items_tax_invoiced' => 'decimal',
    'gw_card_base_tax_invoiced' => 'decimal',
    'gw_card_tax_invoiced' => 'decimal',
    'gw_base_price_refunded' => 'decimal',
    'gw_price_refunded' => 'decimal',
    'gw_items_base_price_refunded' => 'decimal',
    'gw_items_price_refunded' => 'decimal',
    'gw_card_base_price_refunded' => 'decimal',
    'gw_card_price_refunded' => 'decimal',
    'gw_base_tax_amount_refunded' => 'decimal',
    'gw_tax_amount_refunded' => 'decimal',
    'gw_items_base_tax_refunded' => 'decimal',
    'gw_items_tax_refunded' => 'decimal',
    'gw_card_base_tax_refunded' => 'decimal',
    'gw_card_tax_refunded' => 'decimal'
);
foreach ($entityAttributesCodes as $code => $type) {
    $installer->addAttribute('order', $code, array('type' => $type, 'visible' => false));
}

$itemsAttributesCodes = array(
    'gw_base_price_invoiced' => 'decimal',
    'gw_price_invoiced' => 'decimal',
    'gw_base_tax_amount_invoiced' => 'decimal',
    'gw_tax_amount_invoiced' => 'decimal',
    'gw_base_price_refunded' => 'decimal',
    'gw_price_refunded' => 'decimal',
    'gw_base_tax_amount_refunded' => 'decimal',
    'gw_tax_amount_refunded' => 'decimal'
);
foreach ($itemsAttributesCodes as $code => $type) {
    $installer->addAttribute('order_item', $code, array('type' => $type, 'visible' => false));
}

$entityAttributesCodes = array(
    'gw_base_price' => 'decimal',
    'gw_price' => 'decimal',
    'gw_items_base_price' => 'decimal',
    'gw_items_price' => 'decimal',
    'gw_card_base_price' => 'decimal',
    'gw_card_price' => 'decimal',
    'gw_base_tax_amount' => 'decimal',
    'gw_tax_amount' => 'decimal',
    'gw_items_base_tax_amount' => 'decimal',
    'gw_items_tax_amount' => 'decimal',
    'gw_card_base_tax_amount' => 'decimal',
    'gw_card_tax_amount' => 'decimal'
);
foreach ($entityAttributesCodes as $code => $type) {
    $installer->addAttribute('invoice', $code, array('type' => $type));
    $installer->addAttribute('creditmemo', $code, array('type' => $type));
}


/**
 * Add gift wrapping attributes for catalog product entity
 */
$applyTo = join(',', $this->getRealProductTypes());

$installer = $this->getCatalogSetup();

$installer->addAttribute(
    \Magento\Catalog\Model\Product::ENTITY,
    'gift_wrapping_available',
    array(
        'group' => 'Gift Options',
        'backend' => 'Magento\Catalog\Model\Product\Attribute\Backend\Boolean',
        'frontend' => '',
        'label' => 'Allow Gift Wrapping',
        'input' => 'select',
        'source' => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean',
        'global' => \Magento\Catalog\Model\Resource\Eav\Attribute::SCOPE_STORE,
        'visible' => true,
        'required' => false,
        'user_defined' => false,
        'default' => '',
        'apply_to' => $applyTo,
        'frontend_class' => 'hidden-for-virtual',
        'frontend_input_renderer' => 'Magento\GiftWrapping\Block\Adminhtml\Product\Helper\Form\Config',
        'input_renderer' => 'Magento\GiftWrapping\Block\Adminhtml\Product\Helper\Form\Config',
        'visible_on_front' => false
    )
);

$installer->addAttribute(
    \Magento\Catalog\Model\Product::ENTITY,
    'gift_wrapping_price',
    array(
        'group' => 'Gift Options',
        'type' => 'decimal',
        'backend' => 'Magento\Catalog\Model\Product\Attribute\Backend\Price',
        'frontend' => '',
        'label' => 'Price for Gift Wrapping',
        'input' => 'price',
        'global' => \Magento\Catalog\Model\Resource\Eav\Attribute::SCOPE_WEBSITE,
        'visible' => true,
        'required' => false,
        'user_defined' => false,
        'apply_to' => $applyTo,
        'frontend_class' => 'hidden-for-virtual',
        'visible_on_front' => false
    )
);
