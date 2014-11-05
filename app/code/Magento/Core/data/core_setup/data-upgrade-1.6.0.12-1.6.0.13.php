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

$themeDbCollection = $this->createThemeResourceFactory();
$themeDbCollection->setItemObjectClass('Magento\Core\Model\Theme\Data');

/** @var $theme \Magento\Framework\View\Design\ThemeInterface */
foreach ($fileCollection as $theme) {
    $dbTheme = $themeDbCollection->getThemeByFullPath($theme->getFullPath());
    $dbTheme->setCode($theme->getCode());
    $dbTheme->save();
}
