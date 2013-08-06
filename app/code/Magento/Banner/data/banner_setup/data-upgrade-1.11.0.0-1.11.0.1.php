<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Banner
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $installer Enterprise_Enterprise_Model_Resource_Setup_Migration */
$installer = Mage::getResourceModel('Enterprise_Enterprise_Model_Resource_Setup_Migration',
    array('resourceName' => 'core_setup'));
$installer->startSetup();

$installer->appendClassAliasReplace('magento_banner_content', 'banner_content',
    Enterprise_Enterprise_Model_Resource_Setup_Migration::ENTITY_TYPE_BLOCK,
    Enterprise_Enterprise_Model_Resource_Setup_Migration::FIELD_CONTENT_TYPE_WIKI,
    array('banner_id', 'store_id')
);
$installer->doUpdateClassAliases();

$installer->endSetup();
