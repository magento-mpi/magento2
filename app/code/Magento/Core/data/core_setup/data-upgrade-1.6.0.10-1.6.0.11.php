<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $filesystemCollection Magento_Core_Model_Theme_Collection */
$filesystemCollection = $this->_themeFactory->create();
$filesystemCollection->addDefaultPattern('*');

/** @var $theme Magento_Core_Model_Theme */
foreach ($this->_themeResourceFactory->create() as $theme) {
    $themeType = $filesystemCollection->hasTheme($theme)
        ? Magento_Core_Model_Theme::TYPE_PHYSICAL
        : Magento_Core_Model_Theme::TYPE_VIRTUAL;
    $theme->setType($themeType)->save();
}
