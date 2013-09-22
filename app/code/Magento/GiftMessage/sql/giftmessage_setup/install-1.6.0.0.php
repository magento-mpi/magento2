<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftMessage
 * @copyright   {copyright}
 * @license     {license_link}
 */


/** @var $installer \Magento\GiftMessage\Model\Resource\Setup */

$installer = $this;
$installer->startSetup();

/**
 * Create table 'gift_message'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('gift_message'))
    ->addColumn('gift_message_id', \Magento\DB\Ddl\Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'GiftMessage Id')
    ->addColumn('customer_id', \Magento\DB\Ddl\Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Customer id')
    ->addColumn('sender', \Magento\DB\Ddl\Table::TYPE_TEXT, 255, array(
        ), 'Sender')
    ->addColumn('recipient', \Magento\DB\Ddl\Table::TYPE_TEXT, 255, array(
        ), 'Recipient')
    ->addColumn('message', \Magento\DB\Ddl\Table::TYPE_TEXT, null, array(
        ), 'Message')
    ->setComment('Gift Message');

$installer->getConnection()->createTable($table);

/**
 * Add 'gift_message_id' attributes for entities
 */
$entities = array(
    'quote',
    'quote_address',
    'quote_item',
    'quote_address_item',
    'order',
    'order_item'
);
$options = array(
    'type'     => \Magento\DB\Ddl\Table::TYPE_INTEGER,
    'visible'  => false,
    'required' => false
);
foreach ($entities as $entity) {
    $installer->addAttribute($entity, 'gift_message_id', $options);
}

/**
 * Add 'gift_message_available' attributes for entities
 */
$installer->addAttribute('order_item', 'gift_message_available', $options); \Mage::getResourceModel('Magento\Catalog\Model\Resource\Setup', array('resourceName' => 'catalog_setup'))->addAttribute(
$installer->createGiftMessageSetup(array('resourceName' => 'catalog_setup'))->addAttribute(
    \Magento\Catalog\Model\Product::ENTITY, 'gift_message_available',
    array(
        'group'         => 'Gift Options',
        'backend'       => 'Magento\Catalog\Model\Product\Attribute\Backend\Boolean',
        'frontend'      => '',
        'label'         => 'Allow Gift Message',
        'input'         => 'select',
        'class'         => '',
        'source'        => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean',
        'global'        => true,
        'visible'       => true,
        'required'      => false,
        'user_defined'  => false,
        'default'       => '',
        'apply_to'      => '',
        'input_renderer'   => 'Magento\GiftMessage\Block\Adminhtml\Product\Helper\Form\Config',
        'is_configurable'  => 0,
        'visible_on_front' => false
    )
);

$installer->endSetup();
