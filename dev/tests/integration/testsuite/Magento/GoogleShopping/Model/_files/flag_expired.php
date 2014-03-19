<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Expired flag for the google shopping synchronization
 */
$objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();

 /** @var $flag \Magento\GoogleShopping\Model\Flag */
$flag = $objectManager->create('Magento\GoogleShopping\Model\Flag');
$flag->lock();

/** @var $flagResource \Magento\Flag\Resource */
$flagResource = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
    ->create('Magento\Flag\Resource');
$flag->setLastUpdate(date('Y-m-d H:i:s', time() - \Magento\GoogleShopping\Model\Flag::FLAG_TTL - 1));
$flagResource->save($flag);
