<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $this \Magento\Framework\Module\DataSetup */
$installer = $this->createMigrationSetup();
$installer->startSetup();

$installer->appendClassAliasReplace(
    'widget_instance',
    'instance_type',
    \Magento\Framework\Module\Setup\Migration::ENTITY_TYPE_BLOCK,
    \Magento\Framework\Module\Setup\Migration::FIELD_CONTENT_TYPE_PLAIN,
    ['instance_id']
);
$installer->doUpdateClassAliases();

$installer->endSetup();
