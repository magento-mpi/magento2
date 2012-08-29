<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Cms
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $installer Mage_Core_Model_Resource_Setup_Migration */
$installer = Mage::getResourceModel('Mage_Core_Model_Resource_Setup_Migration', 'core_setup');
$installer->startSetup();

$tables = array(
    'cms_block' => array(
        'content' => Mage_Core_Model_Resource_Setup_Migration::FIELD_CONTENT_TYPE_WIKI,
    ),
    'cms_page' => array(
        'content'                  => Mage_Core_Model_Resource_Setup_Migration::FIELD_CONTENT_TYPE_WIKI,
        'layout_update_xml'        => Mage_Core_Model_Resource_Setup_Migration::FIELD_CONTENT_TYPE_XML,
        'custom_layout_update_xml' => Mage_Core_Model_Resource_Setup_Migration::FIELD_CONTENT_TYPE_XML,
    ),
);

foreach ($tables as $table => $fields) {
    foreach ($fields as $field => $contentType) {
        $installer->appendClassAliasReplace(
            $table,
            $field,
            Mage_Core_Model_Resource_Setup_Migration::ENTITY_TYPE_BLOCK,
            $contentType
        );
    }
}

$installer->doUpdateClassAliases();

$installer->endSetup();

