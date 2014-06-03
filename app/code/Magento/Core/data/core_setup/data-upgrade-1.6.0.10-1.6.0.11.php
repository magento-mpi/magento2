<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $filesystemCollection \Magento\Core\Model\Theme\Collection */
$filesystemCollection = $this->createThemeFactory();
$filesystemCollection->addDefaultPattern('*');

/** @var $theme \Magento\Framework\View\Design\ThemeInterface */
foreach ($this->createThemeResourceFactory() as $theme) {
    $themeType = $filesystemCollection->hasTheme($theme)
        ? \Magento\Framework\View\Design\ThemeInterface::TYPE_PHYSICAL
        : \Magento\Framework\View\Design\ThemeInterface::TYPE_VIRTUAL;
    $theme->setType($themeType)->save();
}
