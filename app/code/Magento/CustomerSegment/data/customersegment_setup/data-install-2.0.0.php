<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

/* @var $installer \Magento\CustomerSegment\Model\Resource\Setup */
$installer = $this;

$installer->startSetup();
// use specific attributes for customer segments
$attributesOfEntities = [
    'customer' => [
        'dob',
        'email',
        'firstname',
        'group_id',
        'lastname',
        'gender',
        'default_billing',
        'default_shipping',
        'created_at',
    ],
    'customer_address' => [
        'firstname',
        'lastname',
        'company',
        'street',
        'city',
        'region_id',
        'postcode',
        'country_id',
        'telephone',
    ],
    'order_address' => [
        'firstname',
        'lastname',
        'company',
        'street',
        'city',
        'region_id',
        'postcode',
        'country_id',
        'telephone',
        'email',
    ],
];

foreach ($attributesOfEntities as $entityTypeId => $attributes) {
    foreach ($attributes as $attributeCode) {
        $installer->updateAttribute($entityTypeId, $attributeCode, 'is_used_for_customer_segment', '1');
    }
}

/**
 * Resave all segments for segment conditions regeneration
 */
/** @var $this \Magento\CustomerSegment\Model\Resource\Setup */
$collection = $this->createSegmentCollection();
/** @var $segment \Magento\CustomerSegment\Model\Segment */
foreach ($collection as $segment) {
    $segment->afterLoad();
    $segment->save();
}

$installer = $this->createMigrationSetup();

$installer->appendClassAliasReplace(
    'magento_customersegment_segment',
    'conditions_serialized',
    \Magento\Framework\Module\Setup\Migration::ENTITY_TYPE_MODEL,
    \Magento\Framework\Module\Setup\Migration::FIELD_CONTENT_TYPE_SERIALIZED,
    ['segment_id']
);

$installer->doUpdateClassAliases();

$installer->endSetup();
