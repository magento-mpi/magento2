<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Cms
 * @copyright   {copyright}
 * @license     {license_link}
 */
/** @var \Magento\Cms\Model\Resource\Setup $this */
/** @var $installer \Magento\Core\Model\Resource\Setup\Migration */
$installer = $this->createMigrationSetup(array('resourceName' => 'core_setup'));;
$installer->startSetup();

$installer->appendClassAliasReplace('cms_block', 'content',
    \Magento\Core\Model\Resource\Setup\Migration::ENTITY_TYPE_BLOCK,
    \Magento\Core\Model\Resource\Setup\Migration::FIELD_CONTENT_TYPE_WIKI,
    array('block_id')
);
$installer->appendClassAliasReplace('cms_page', 'content',
    \Magento\Core\Model\Resource\Setup\Migration::ENTITY_TYPE_BLOCK,
    \Magento\Core\Model\Resource\Setup\Migration::FIELD_CONTENT_TYPE_WIKI,
    array('page_id')
);
$installer->appendClassAliasReplace('cms_page', 'layout_update_xml',
    \Magento\Core\Model\Resource\Setup\Migration::ENTITY_TYPE_BLOCK,
    \Magento\Core\Model\Resource\Setup\Migration::FIELD_CONTENT_TYPE_XML,
    array('page_id')
);
$installer->appendClassAliasReplace('cms_page', 'custom_layout_update_xml',
    \Magento\Core\Model\Resource\Setup\Migration::ENTITY_TYPE_BLOCK,
    \Magento\Core\Model\Resource\Setup\Migration::FIELD_CONTENT_TYPE_XML,
    array('page_id')
);

$installer->doUpdateClassAliases();

$installer->endSetup();

