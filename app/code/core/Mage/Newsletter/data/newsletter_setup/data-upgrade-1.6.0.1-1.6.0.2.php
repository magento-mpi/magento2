<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Newsletter
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $installer Mage_Core_Model_Resource_Setup_Migration */
$installer = Mage::getResourceModel('Mage_Core_Model_Resource_Setup_Migration', 'core_setup');
$installer->startSetup();

$tables = array(
    'newsletter_template' => array('template_text', 'template_text_preprocessed'),
    'newsletter_queue'    => array('newsletter_text'),
);

foreach ($tables as $table => $fields) {
    foreach ($fields as $field) {
        $installer->appendClassAliasReplace($table, $field,
            Mage_Core_Model_Resource_Setup_Migration::ENTITY_TYPE_BLOCK,
            Mage_Core_Model_Resource_Setup_Migration::FIELD_CONTENT_TYPE_WIKI
        );
    }
}

$installer->doUpdateClassAliases();

$installer->endSetup();
