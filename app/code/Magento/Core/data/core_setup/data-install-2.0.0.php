<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $this \Magento\Core\Model\Resource\Setup */
$installer = $this->createMigrationSetup();
$installer->startSetup();

$installer->appendClassAliasReplace(
    'core_config_data',
    'value',
    \Magento\Framework\Module\Setup\Migration::ENTITY_TYPE_MODEL,
    \Magento\Framework\Module\Setup\Migration::FIELD_CONTENT_TYPE_PLAIN,
    array('config_id')
);
$installer->appendClassAliasReplace(
    'core_layout_update',
    'xml',
    \Magento\Framework\Module\Setup\Migration::ENTITY_TYPE_BLOCK,
    \Magento\Framework\Module\Setup\Migration::FIELD_CONTENT_TYPE_XML,
    array('layout_update_id')
);
$installer->doUpdateClassAliases();

/**
 * Delete rows by condition from authorization_rule
 */
$tableName = $installer->getTable('authorization_rule');
if ($tableName) {
    $installer->getConnection()->delete($tableName, array('resource_id = ?' => 'admin/system/tools/compiler'));
}

/**
 * Delete rows by condition from core_resource
 */
$tableName = $installer->getTable('core_resource');
if ($tableName) {
    $installer->getConnection()->delete($tableName, array('code = ?' => 'admin_setup'));
}

/**
 * Update rows in core_theme
 */
$installer->getConnection()->update(
    $installer->getTable('core_theme'),
    array('area' => 'frontend'), array('area = ?' => '')
);

/**
 * Update theme's data
 */
$fileCollection = $this->createThemeFactory();
$fileCollection->addDefaultPattern('*');
$fileCollection->setItemObjectClass('Magento\Core\Model\Theme\Data');

$resourceCollection = $this->createThemeResourceFactory();
$resourceCollection->setItemObjectClass('Magento\Core\Model\Theme\Data');

/** @var $theme \Magento\Framework\View\Design\ThemeInterface */
foreach ($resourceCollection as $theme) {
    $themeType = $fileCollection->hasTheme($theme)
        ? \Magento\Framework\View\Design\ThemeInterface::TYPE_PHYSICAL
        : \Magento\Framework\View\Design\ThemeInterface::TYPE_VIRTUAL;
    $theme->setType($themeType)->save();
}

/** @var $theme \Magento\Framework\View\Design\ThemeInterface */
foreach ($fileCollection as $theme) {
    $dbTheme = $themeDbCollection->getThemeByFullPath($theme->getFullPath());
    $dbTheme->setCode($theme->getCode());
    $dbTheme->save();
}

$installer->endSetup();
$installer->getEventManager()->dispatch('theme_registration_from_filesystem');
