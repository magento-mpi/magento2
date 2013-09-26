<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Newsletter
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var Magento_Newsletter_Model_Resource_Setup $this */

$installer = $this->getSetupMigration();
$installer->startSetup();

$installer->appendClassAliasReplace('newsletter_template', 'template_text',
    Magento_Core_Model_Resource_Setup_Migration::ENTITY_TYPE_BLOCK,
    Magento_Core_Model_Resource_Setup_Migration::FIELD_CONTENT_TYPE_WIKI,
    array('template_id')
);
$installer->appendClassAliasReplace('newsletter_template', 'template_text_preprocessed',
    Magento_Core_Model_Resource_Setup_Migration::ENTITY_TYPE_BLOCK,
    Magento_Core_Model_Resource_Setup_Migration::FIELD_CONTENT_TYPE_WIKI,
    array('template_id')
);
$installer->appendClassAliasReplace('newsletter_queue', 'newsletter_text',
    Magento_Core_Model_Resource_Setup_Migration::ENTITY_TYPE_BLOCK,
    Magento_Core_Model_Resource_Setup_Migration::FIELD_CONTENT_TYPE_WIKI,
    array('queue_id')
);

$installer->doUpdateClassAliases();

$installer->endSetup();
