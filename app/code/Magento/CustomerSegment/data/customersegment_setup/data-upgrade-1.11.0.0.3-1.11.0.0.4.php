<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $this \Magento\CustomerSegment\Model\Resource\Setup */
$installer = $this->createMigrationSetup();

$installer->startSetup();

$installer->appendClassAliasReplace(
    'magento_customersegment_segment',
    'conditions_serialized',
    \Magento\Framework\Module\Setup\Migration::ENTITY_TYPE_MODEL,
    \Magento\Framework\Module\Setup\Migration::FIELD_CONTENT_TYPE_SERIALIZED,
    array('segment_id')
);
$installer->doUpdateClassAliases();

$installer->endSetup();
