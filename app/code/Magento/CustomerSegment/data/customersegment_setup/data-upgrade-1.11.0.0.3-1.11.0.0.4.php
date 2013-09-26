<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CustomerSegment
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $installer Magento_Enterprise_Model_Resource_Setup_Migration */
$installer = $this->createSetupMigration(array('resourceName' => 'core_setup'));

$installer->startSetup();

$installer->appendClassAliasReplace('magento_customersegment_segment', 'conditions_serialized',
    Magento_Enterprise_Model_Resource_Setup_Migration::ENTITY_TYPE_MODEL,
    Magento_Enterprise_Model_Resource_Setup_Migration::FIELD_CONTENT_TYPE_SERIALIZED,
    array('segment_id')
);
$installer->doUpdateClassAliases();

$installer->endSetup();
