<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $this \Magento\Framework\Module\Setup */
$installer = $this->createMigrationSetup();
$installer->startSetup();

$installer->appendClassAliasReplace(
    'newsletter_template',
    'template_text',
    \Magento\Framework\Module\Setup\Migration::ENTITY_TYPE_BLOCK,
    \Magento\Framework\Module\Setup\Migration::FIELD_CONTENT_TYPE_WIKI,
    array('template_id')
);
$installer->appendClassAliasReplace(
    'newsletter_template',
    'template_text_preprocessed',
    \Magento\Framework\Module\Setup\Migration::ENTITY_TYPE_BLOCK,
    \Magento\Framework\Module\Setup\Migration::FIELD_CONTENT_TYPE_WIKI,
    array('template_id')
);
$installer->appendClassAliasReplace(
    'newsletter_queue',
    'newsletter_text',
    \Magento\Framework\Module\Setup\Migration::ENTITY_TYPE_BLOCK,
    \Magento\Framework\Module\Setup\Migration::FIELD_CONTENT_TYPE_WIKI,
    array('queue_id')
);

$installer->doUpdateClassAliases();

$installer->endSetup();
