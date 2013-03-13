<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */


/** @var $themeCollection Mage_Core_Model_Resource_Theme_Collection */
$themeCollection = Mage::getResourceModel('Mage_Core_Model_Resource_Theme_Collection');
/** @var $theme Mage_Core_Model_Theme */
foreach ($themeCollection as $theme) {
    $themeType = $theme->isPresentInFilesystem()
        ? Mage_Core_Model_Theme::TYPE_PHYSICAL
        : Mage_Core_Model_Theme::TYPE_VIRTUAL;
    $theme->setType($themeType)->save();
}
