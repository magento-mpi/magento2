<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $installer \Magento\GiftMessage\Model\Resource\Setup */
$installer = $this;
/**
 * Add 'gift_message_id' attributes for entities
 */
$entities = array('quote', 'quote_address', 'quote_item', 'quote_address_item', 'order', 'order_item');
$options = array('type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 'visible' => false, 'required' => false);
foreach ($entities as $entity) {
    $installer->addAttribute($entity, 'gift_message_id', $options);
}

/**
 * Add 'gift_message_available' attributes for entities
 */
$installer->addAttribute('order_item', 'gift_message_available', $options);
$installer->createGiftMessageSetup(
    array('resourceName' => 'catalog_setup')
)->addAttribute(
    \Magento\Catalog\Model\Product::ENTITY,
    'gift_message_available',
    array(
        'group' => 'Gift Options',
        'backend' => 'Magento\Catalog\Model\Product\Attribute\Backend\Boolean',
        'frontend' => '',
        'label' => 'Allow Gift Message',
        'input' => 'select',
        'class' => '',
        'source' => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean',
        'global' => true,
        'visible' => true,
        'required' => false,
        'user_defined' => false,
        'default' => '',
        'apply_to' => '',
        'input_renderer' => 'Magento\GiftMessage\Block\Adminhtml\Product\Helper\Form\Config',
        'visible_on_front' => false
    )
);
