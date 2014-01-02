<?php
/**
 * Expired flag for the google shopping synchronization
 */
$objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();

 /** @var $flag \Magento\GoogleShopping\Model\Flag */
$flag = $objectManager->create('Magento\GoogleShopping\Model\Flag');
$flag->lock();

/** @var $flagResource \Magento\Core\Model\Resource\Flag */
$flagResource = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
    ->create('Magento\Core\Model\Resource\Flag');
$flag->setLastUpdate(date('Y-m-d H:i:s', time() - \Magento\GoogleShopping\Model\Flag::FLAG_TTL - 1));
$flagResource->save($flag);
