<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CustomerSegment
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $installer \Magento\Module\Setup\Migration */
$installer = $this->createMigrationSetup();

$installer->startSetup();

$installer->appendClassAliasReplace(
    'magento_customersegment_segment',
    'conditions_serialized',
    \Magento\Module\Setup\Migration::ENTITY_TYPE_MODEL,
    \Magento\Module\Setup\Migration::FIELD_CONTENT_TYPE_SERIALIZED,
    array('segment_id')
);
$installer->doUpdateClassAliases();

$installer->endSetup();
