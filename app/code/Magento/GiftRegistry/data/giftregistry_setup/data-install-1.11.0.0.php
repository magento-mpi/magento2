<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $installer \Magento\GiftRegistry\Model\Resource\Setup */
$installer = $this;
/**
 * Add attributes
 */
$installer->addAttribute(
    'quote_item',
    'giftregistry_item_id',
    array('type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 'visible' => false)
);
$installer->addAttribute(
    'order_item',
    'giftregistry_item_id',
    array('type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 'visible' => false)
);
$installer->addAttribute(
    'quote_address',
    'giftregistry_item_id',
    array('type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 'visible' => false)
);
$installer->addAttribute(
    'order_address',
    'giftregistry_item_id',
    array('type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 'visible' => false)
);

$typesData = array(
    array(
        'code' => 'birthday',
        'meta_xml' => '<config><prototype><registry><event_date><label>Event Date</label><group>event_information</group><type>date</type><sort_order>5</sort_order><date_format>short</date_format><frontend><is_required>1</is_required><is_searcheable>1</is_searcheable><is_listed>1</is_listed></frontend></event_date><event_country><label>Country</label><group>event_information</group><type>country</type><sort_order>1</sort_order><show_region>1</show_region><frontend><is_required>1</is_required><is_searcheable>1</is_searcheable><is_listed>1</is_listed></frontend></event_country></registry></prototype></config>',
        'store_id' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
        'label' => 'Birthday',
        'is_listed' => 1,
        'sort_order' => 1
    ),
    array(
        'code' => 'baby_registry',
        'meta_xml' => '<config><prototype><registrant><role><label>Role</label><group>registrant</group><type>select</type><sort_order>1</sort_order><options><mom>Mother</mom><dad>Father</dad></options><frontend><is_required>1</is_required><is_searcheable>1</is_searcheable><is_listed>1</is_listed></frontend></role></registrant><registry><baby_gender><label>Baby Gender</label><group>registry</group><type>select</type><sort_order>5</sort_order><options><boy>Boy</boy><girl>Girl</girl><surprise>Surprise</surprise></options><default>surprise</default><frontend><is_required>1</is_required></frontend></baby_gender><event_country><label>Country</label><group>event_information</group><type>country</type><sort_order>1</sort_order><show_region>1</show_region><frontend><is_required>1</is_required><is_searcheable>1</is_searcheable><is_listed>1</is_listed></frontend></event_country></registry></prototype></config>',
        'store_id' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
        'label' => 'Baby Registry',
        'is_listed' => 1,
        'sort_order' => 5
    ),
    array(
        'code' => 'wedding',
        'meta_xml' => '<config><prototype><registrant><role><label>Role</label><group>registrant</group><type>select</type><sort_order>20</sort_order><options><groom>Groom</groom><bride>Bride</bride><partner>Partner</partner></options><frontend><is_required>1</is_required><is_searcheable>0</is_searcheable><is_listed>1</is_listed></frontend></role></registrant><registry><event_country><label>Country</label><group>event_information</group><type>country</type><sort_order>1</sort_order><show_region>1</show_region><frontend><is_required>1</is_required><is_searcheable>1</is_searcheable><is_listed>1</is_listed></frontend></event_country><event_date><label>Wedding Date</label><group>event_information</group><type>date</type><sort_order>5</sort_order><date_format>short</date_format><frontend><is_required>1</is_required><is_searcheable>1</is_searcheable><is_listed>1</is_listed></frontend></event_date><event_location><label>Location</label><group>event_information</group><type>text</type><sort_order>10</sort_order><frontend><is_required>1</is_required><is_searcheable>1</is_searcheable><is_listed>1</is_listed></frontend></event_location><number_of_guests><label>Number of Guests</label><group>event_information</group><type>text</type><sort_order>15</sort_order><frontend><is_required>1</is_required></frontend></number_of_guests></registry></prototype></config>',
        'store_id' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
        'label' => 'Wedding',
        'is_listed' => 1,
        'sort_order' => 10
    )
);

foreach ($typesData as $data) {
    $this->_typeFactory->create()->addData($data)->setStoreId($data['store_id'])->save();
}
