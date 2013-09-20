<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Creates banner with enabled status and text content
 */

/** @var $banner \Magento\Banner\Model\Banner */
$banner = \Mage::getModel('Magento\Banner\Model\Banner');

$banner->setIsEnabled(\Magento\Banner\Model\Banner::STATUS_ENABLED)
    ->setName('Test Banner')
    ->setTypes('')
    ->setStoreContents(array(0 => 'Banner Content'))
    ->save();

