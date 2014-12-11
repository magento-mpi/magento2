<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

/* @var $this \Magento\GiftWrapping\Model\Resource\Setup */
$installer = $this;
/**
 * Add gift wrapping attributes for sales entities
 */
$entityAttributesCodes = [
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
    'gw_card_tax_amount' => 'decimal',
];
foreach ($entityAttributesCodes as $code => $type) {
    $installer->addAttribute('quote', $code, ['type' => $type, 'visible' => false]);
    $installer->addAttribute('quote_address', $code, ['type' => $type, 'visible' => false]);
    $installer->addAttribute('order', $code, ['type' => $type, 'visible' => false]);
}

$itemsAttributesCodes = [
    'gw_id' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
    'gw_base_price' => 'decimal',
    'gw_price' => 'decimal',
    'gw_base_tax_amount' => 'decimal',
    'gw_tax_amount' => 'decimal',
];
foreach ($itemsAttributesCodes as $code => $type) {
    $installer->addAttribute('quote_item', $code, ['type' => $type, 'visible' => false]);
    $installer->addAttribute('quote_address_item', $code, ['type' => $type, 'visible' => false]);
    $installer->addAttribute('order_item', $code, ['type' => $type, 'visible' => false]);
}

$entityAttributesCodes = [
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
    'gw_card_tax_refunded' => 'decimal',
];
foreach ($entityAttributesCodes as $code => $type) {
    $installer->addAttribute('order', $code, ['type' => $type, 'visible' => false]);
}

$itemsAttributesCodes = [
    'gw_base_price_invoiced' => 'decimal',
    'gw_price_invoiced' => 'decimal',
    'gw_base_tax_amount_invoiced' => 'decimal',
    'gw_tax_amount_invoiced' => 'decimal',
    'gw_base_price_refunded' => 'decimal',
    'gw_price_refunded' => 'decimal',
    'gw_base_tax_amount_refunded' => 'decimal',
    'gw_tax_amount_refunded' => 'decimal',
];
foreach ($itemsAttributesCodes as $code => $type) {
    $installer->addAttribute('order_item', $code, ['type' => $type, 'visible' => false]);
}

$entityAttributesCodes = [
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
    'gw_card_tax_amount' => 'decimal',
];
foreach ($entityAttributesCodes as $code => $type) {
    $installer->addAttribute('invoice', $code, ['type' => $type]);
    $installer->addAttribute('creditmemo', $code, ['type' => $type]);
}

/**
 * Add gift wrapping attributes for catalog product entity
 */
$applyTo = join(',', $this->getRealProductTypes());

$installer = $this->getCatalogSetup();

$installer->addAttribute(
    \Magento\Catalog\Model\Product::ENTITY,
    'gift_wrapping_available',
    [
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
    ]
);

$installer->addAttribute(
    \Magento\Catalog\Model\Product::ENTITY,
    'gift_wrapping_price',
    [
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
    ]
);

/** @var $this \Magento\GiftWrapping\Model\Resource\Setup */

$groupName = 'Autosettings';
$entityTypeId = $this->getEntityTypeId(\Magento\Catalog\Model\Product::ENTITY);
$attributeSetId = $this->getAttributeSetId($entityTypeId, 'Default');

$attributesOrder = ['gift_wrapping_available' => 70, 'gift_wrapping_price' => 80];

foreach ($attributesOrder as $key => $value) {
    $attribute = $this->getAttribute($entityTypeId, $key);
    if ($attribute) {
        $this->addAttributeToGroup($entityTypeId, $attributeSetId, $groupName, $attribute['attribute_id'], $value);
    }
}

if (!$this->getAttributesNumberInGroup($entityTypeId, $attributeSetId, 'Gift Options')) {
    $this->removeAttributeGroup($entityTypeId, $attributeSetId, 'Gift Options');
}
