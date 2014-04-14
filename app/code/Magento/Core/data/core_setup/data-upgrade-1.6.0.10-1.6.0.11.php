<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $filesystemCollection \Magento\Core\Model\Theme\Collection */
$filesystemCollection = $this->createThemeFactory();
$filesystemCollection->addDefaultPattern('*');

/** @var $theme \Magento\View\Design\ThemeInterface */
foreach ($this->createThemeResourceFactory() as $theme) {
    $themeType = $filesystemCollection->hasTheme(
        $theme
    ) ? \Magento\View\Design\ThemeInterface::TYPE_PHYSICAL : \Magento\View\Design\ThemeInterface::TYPE_VIRTUAL;
    $theme->setType($themeType)->save();
}
