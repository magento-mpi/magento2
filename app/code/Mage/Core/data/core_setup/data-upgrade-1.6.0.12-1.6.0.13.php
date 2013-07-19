<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $themeDbCollection Mage_Core_Model_Resource_Theme_Collection */
$themeDbCollection = Mage::getResourceModel('Mage_Core_Model_Resource_Theme_Collection');

/** @var $themeFsCollection Mage_Core_Model_Theme_Collection */
$themeFsCollection = Mage::getModel('Mage_Core_Model_Theme_Collection');
/** @var $theme Mage_Core_Model_Theme */
foreach ($themeFsCollection->addDefaultPattern('*') as $theme) {
    $dbTheme = $themeDbCollection->getThemeByFullPath($theme->getFullPath());
    $dbTheme->setCode($theme->getCode());
    $dbTheme->save();
}
