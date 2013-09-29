<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Newsletter
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var \Magento\Newsletter\Model\Resource\Setup $this */

$installer = $this->getSetupMigration();
$installer->startSetup();

$installer->appendClassAliasReplace('newsletter_template', 'template_text',
    \Magento\Core\Model\Resource\Setup\Migration::ENTITY_TYPE_BLOCK,
    \Magento\Core\Model\Resource\Setup\Migration::FIELD_CONTENT_TYPE_WIKI,
    array('template_id')
);
$installer->appendClassAliasReplace('newsletter_template', 'template_text_preprocessed',
    \Magento\Core\Model\Resource\Setup\Migration::ENTITY_TYPE_BLOCK,
    \Magento\Core\Model\Resource\Setup\Migration::FIELD_CONTENT_TYPE_WIKI,
    array('template_id')
);
$installer->appendClassAliasReplace('newsletter_queue', 'newsletter_text',
    \Magento\Core\Model\Resource\Setup\Migration::ENTITY_TYPE_BLOCK,
    \Magento\Core\Model\Resource\Setup\Migration::FIELD_CONTENT_TYPE_WIKI,
    array('queue_id')
);

$installer->doUpdateClassAliases();

$installer->endSetup();
