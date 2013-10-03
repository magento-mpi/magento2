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
$filesystemCollection = $this->_themeFactory->create();
$filesystemCollection->addDefaultPattern('*');

/** @var $theme \Magento\Core\Model\Theme */
foreach ($this->_themeResourceFactory->create() as $theme) {
    $themeType = $filesystemCollection->hasTheme($theme)
        ? \Magento\Core\Model\Theme::TYPE_PHYSICAL
        : \Magento\Core\Model\Theme::TYPE_VIRTUAL;
    $theme->setType($themeType)->save();
}
