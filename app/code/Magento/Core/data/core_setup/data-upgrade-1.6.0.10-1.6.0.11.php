<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */


/** @var $resourceCollection \Magento\Core\Model\Resource\Theme\Collection */
$resourceCollection = \Mage::getResourceModel('Magento\Core\Model\Resource\Theme\Collection');

/** @var $filesystemCollection \Magento\Core\Model\Theme\Collection */
$filesystemCollection = \Mage::getModel('Magento\Core\Model\Theme\Collection');
$filesystemCollection->addDefaultPattern('*');

/** @var $theme \Magento\Core\Model\Theme */
foreach ($resourceCollection as $theme) {
    $themeType = $filesystemCollection->hasTheme($theme)
        ? \Magento\Core\Model\Theme::TYPE_PHYSICAL
        : \Magento\Core\Model\Theme::TYPE_VIRTUAL;
    $theme->setType($themeType)->save();
}
