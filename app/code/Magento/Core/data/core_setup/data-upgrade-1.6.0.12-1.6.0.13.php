<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $themeDbCollection \Magento\Core\Model\Resource\Theme\Collection */
$themeDbCollection = $this->createThemeResourceFactory();

/** @var $theme \Magento\View\Design\ThemeInterface */
foreach ($this->createThemeFactory()->addDefaultPattern('*') as $theme) {
    $dbTheme = $themeDbCollection->getThemeByFullPath($theme->getFullPath());
    $dbTheme->setCode($theme->getCode());
    $dbTheme->save();
}
