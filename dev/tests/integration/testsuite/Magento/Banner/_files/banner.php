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

/** @var $banner Magento_Banner_Model_Banner */
$banner = Mage::getModel('Magento_Banner_Model_Banner');

$banner->setIsEnabled(Magento_Banner_Model_Banner::STATUS_ENABLED)
    ->setName('Test Banner')
    ->setTypes('')
    ->setStoreContents(array(0 => 'Banner Content'))
    ->save();

