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
$themeDbCollection = \Mage::getResourceModel('Magento\Core\Model\Resource\Theme\Collection');

/** @var $themeFsCollection \Magento\Core\Model\Theme\Collection */
$themeFsCollection = \Mage::getModel('Magento\Core\Model\Theme\Collection');
/** @var $theme \Magento\Core\Model\Theme */
foreach ($themeFsCollection->addDefaultPattern('*') as $theme) {
    $dbTheme = $themeDbCollection->getThemeByFullPath($theme->getFullPath());
    $dbTheme->setCode($theme->getCode());
    $dbTheme->save();
}
