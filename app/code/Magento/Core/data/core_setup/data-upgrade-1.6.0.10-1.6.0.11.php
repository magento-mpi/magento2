<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */


/** @var $resourceCollection Magento_Core_Model_Resource_Theme_Collection */
$resourceCollection = Mage::getResourceModel('Magento_Core_Model_Resource_Theme_Collection');

/** @var $filesystemCollection Magento_Core_Model_Theme_Collection */
$filesystemCollection = Mage::getModel('Magento_Core_Model_Theme_Collection');
$filesystemCollection->addDefaultPattern('*');

/** @var $theme Magento_Core_Model_Theme */
foreach ($resourceCollection as $theme) {
    $themeType = $filesystemCollection->hasTheme($theme)
        ? Magento_Core_Model_Theme::TYPE_PHYSICAL
        : Magento_Core_Model_Theme::TYPE_VIRTUAL;
    $theme->setType($themeType)->save();
}
