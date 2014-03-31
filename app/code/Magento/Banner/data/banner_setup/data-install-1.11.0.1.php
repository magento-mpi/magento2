<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Banner
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $installer \Magento\Module\Setup\Migration */
$installer = $this->_migrationFactory->create(array('resourceName' => 'core_setup'));
$installer->startSetup();

$installer->appendClassAliasReplace(
    'magento_banner_content',
    'banner_content',
    \Magento\Module\Setup\Migration::ENTITY_TYPE_BLOCK,
    \Magento\Module\Setup\Migration::FIELD_CONTENT_TYPE_WIKI,
    array('banner_id', 'store_id')
);
$installer->doUpdateClassAliases();

$installer->endSetup();
