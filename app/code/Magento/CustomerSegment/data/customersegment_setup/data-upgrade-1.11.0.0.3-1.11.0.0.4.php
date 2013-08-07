<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CustomerSegment
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $installer Enterprise_Enterprise_Model_Resource_Setup_Migration */
$installer = Mage::getResourceModel('Enterprise_Enterprise_Model_Resource_Setup_Migration',
    array('resourceName' => 'core_setup'));
$installer->startSetup();

$installer->appendClassAliasReplace('magento_customersegment_segment', 'conditions_serialized',
    Enterprise_Enterprise_Model_Resource_Setup_Migration::ENTITY_TYPE_MODEL,
    Enterprise_Enterprise_Model_Resource_Setup_Migration::FIELD_CONTENT_TYPE_SERIALIZED,
    array('segment_id')
);
$installer->doUpdateClassAliases();

$installer->endSetup();
