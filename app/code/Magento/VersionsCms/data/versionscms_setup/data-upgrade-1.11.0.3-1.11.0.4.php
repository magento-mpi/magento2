<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_VersionsCms
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $installer \Magento\Enterprise\Model\Resource\Setup\Migration */
$installer = \Mage::getResourceModel('Magento\Enterprise\Model\Resource\Setup\Migration',
    array('resourceName' => 'core_setup'));
$installer->startSetup();

$installer->appendClassAliasReplace('magento_versionscms_page_revision', 'content',
    \Magento\Enterprise\Model\Resource\Setup\Migration::ENTITY_TYPE_BLOCK,
    \Magento\Enterprise\Model\Resource\Setup\Migration::FIELD_CONTENT_TYPE_WIKI,
    array('revision_id')
);
$installer->appendClassAliasReplace('magento_versionscms_page_revision', 'layout_update_xml',
    \Magento\Enterprise\Model\Resource\Setup\Migration::ENTITY_TYPE_BLOCK,
    \Magento\Enterprise\Model\Resource\Setup\Migration::FIELD_CONTENT_TYPE_XML,
    array('revision_id')
);
$installer->appendClassAliasReplace('magento_versionscms_page_revision', 'custom_layout_update_xml',
    \Magento\Enterprise\Model\Resource\Setup\Migration::ENTITY_TYPE_BLOCK,
    \Magento\Enterprise\Model\Resource\Setup\Migration::FIELD_CONTENT_TYPE_XML,
    array('revision_id')
);
$installer->doUpdateClassAliases();

$installer->endSetup();
