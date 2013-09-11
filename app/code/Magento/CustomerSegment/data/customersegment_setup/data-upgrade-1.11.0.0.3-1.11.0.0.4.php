<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CustomerSegment
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $installer \Magento\Enterprise\Model\Resource\Setup\Migration */
$installer = \Mage::getResourceModel('Magento\Enterprise\Model\Resource\Setup\Migration',
    array('resourceName' => 'core_setup'));
$installer->startSetup();

$installer->appendClassAliasReplace('magento_customersegment_segment', 'conditions_serialized',
    \Magento\Enterprise\Model\Resource\Setup\Migration::ENTITY_TYPE_MODEL,
    \Magento\Enterprise\Model\Resource\Setup\Migration::FIELD_CONTENT_TYPE_SERIALIZED,
    array('segment_id')
);
$installer->doUpdateClassAliases();

$installer->endSetup();
