<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */


/** @var $resourceCollection Mage_Core_Model_Resource_Theme_Collection */
$resourceCollection = Mage::getResourceModel('Mage_Core_Model_Resource_Theme_Collection');

/** @var $filesystemCollection Mage_Core_Model_Theme_Collection */
$filesystemCollection = Mage::getModel('Mage_Core_Model_Theme_Collection');
$filesystemCollection->addDefaultPattern('*');

/** @var $theme Mage_Core_Model_Theme */
foreach ($resourceCollection as $theme) {
    $themeType = $filesystemCollection->hasTheme($theme)
        ? Mage_Core_Model_Theme::TYPE_PHYSICAL
        : Mage_Core_Model_Theme::TYPE_VIRTUAL;
    $theme->setType($themeType)->save();
}
