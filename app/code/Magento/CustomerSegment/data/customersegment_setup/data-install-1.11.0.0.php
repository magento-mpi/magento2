<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/* @var $installer \Magento\Eav\Model\Entity\Setup */
$installer = $this;
// use specific attributes for customer segments
$attributesOfEntities = array(
    'customer' => array(
        'dob',
        'email',
        'firstname',
        'group_id',
        'lastname',
        'gender',
        'default_billing',
        'default_shipping',
        'created_at'
    ),
    'customer_address' => array(
        'firstname',
        'lastname',
        'company',
        'street',
        'city',
        'region_id',
        'postcode',
        'country_id',
        'telephone'
    ),
    'order_address' => array(
        'firstname',
        'lastname',
        'company',
        'street',
        'city',
        'region_id',
        'postcode',
        'country_id',
        'telephone',
        'email'
    )
);

foreach ($attributesOfEntities as $entityTypeId => $attributes) {
    foreach ($attributes as $attributeCode) {
        $installer->updateAttribute($entityTypeId, $attributeCode, 'is_used_for_customer_segment', '1');
    }
}
