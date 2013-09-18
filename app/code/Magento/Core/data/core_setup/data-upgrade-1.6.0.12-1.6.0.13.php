<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $themeDbCollection Magento_Core_Model_Resource_Theme_Collection */
$themeDbCollection = $this->_themeResourceFactory->create();

/** @var $theme Magento_Core_Model_Theme */
foreach ($this->_themeFactory->create()->addDefaultPattern('*') as $theme) {
    $dbTheme = $themeDbCollection->getThemeByFullPath($theme->getFullPath());
    $dbTheme->setCode($theme->getCode());
    $dbTheme->save();
}
