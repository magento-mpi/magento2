<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $themeDbCollection Magento_Core_Model_Resource_Theme_Collection */
$themeDbCollection = Mage::getResourceModel('Magento_Core_Model_Resource_Theme_Collection');

/** @var $themeFsCollection Magento_Core_Model_Theme_Collection */
$themeFsCollection = Mage::getModel('Magento_Core_Model_Theme_Collection');
/** @var $theme Magento_Core_Model_Theme */
foreach ($themeFsCollection->addDefaultPattern('*') as $theme) {
    $dbTheme = $themeDbCollection->getThemeByFullPath($theme->getFullPath());
    $dbTheme->setCode($theme->getCode());
    $dbTheme->save();
}
