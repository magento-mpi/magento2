<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $this \Magento\Core\Model\Resource\Setup */

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
