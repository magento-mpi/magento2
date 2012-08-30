<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Banner
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $installer Mage_Core_Model_Resource_Setup_Migration */
$installer = Mage::getResourceModel('Enterprise_Enterprise_Model_Resource_Setup_Migration', 'core_setup');
$installer->startSetup();

$installer->appendClassAliasReplace('enterprise_banner_content', 'banner_content',
    Mage_Core_Model_Resource_Setup_Migration::ENTITY_TYPE_BLOCK,
    Mage_Core_Model_Resource_Setup_Migration::FIELD_CONTENT_TYPE_WIKI
);
$installer->doUpdateClassAliases();

$installer->endSetup();
