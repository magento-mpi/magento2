<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/* @var $installer \Magento\Framework\Module\DataSetup */
$installer = $this;

$subscriberTable = $installer->getTable('newsletter_subscriber');

$select = $installer->getConnection()->select()->from(
    array('main_table' => $subscriberTable)
)->join(
    array('customer' => $installer->getTable('customer_entity')),
    'main_table.customer_id = customer.entity_id',
    array('website_id')
)->where(
    'customer.website_id = 0'
);

$installer->getConnection()->query($installer->getConnection()->deleteFromSelect($select, 'main_table'));
