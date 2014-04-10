<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Newsletter
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $installer \Magento\Module\Setup\Migration */
$installer = $this->createMigrationSetup();
$installer->startSetup();

$installer->appendClassAliasReplace(
    'newsletter_template',
    'template_text',
    \Magento\Module\Setup\Migration::ENTITY_TYPE_BLOCK,
    \Magento\Module\Setup\Migration::FIELD_CONTENT_TYPE_WIKI,
    array('template_id')
);
$installer->appendClassAliasReplace(
    'newsletter_template',
    'template_text_preprocessed',
    \Magento\Module\Setup\Migration::ENTITY_TYPE_BLOCK,
    \Magento\Module\Setup\Migration::FIELD_CONTENT_TYPE_WIKI,
    array('template_id')
);
$installer->appendClassAliasReplace(
    'newsletter_queue',
    'newsletter_text',
    \Magento\Module\Setup\Migration::ENTITY_TYPE_BLOCK,
    \Magento\Module\Setup\Migration::FIELD_CONTENT_TYPE_WIKI,
    array('queue_id')
);

$installer->doUpdateClassAliases();

$installer->endSetup();
