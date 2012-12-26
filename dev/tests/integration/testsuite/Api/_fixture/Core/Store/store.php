<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

if (!Magento_Test_TestCase_ApiAbstract::getFixture('store')) {
    $defaultWebsite = Mage::app()->getWebsite();
    /** @var $storeFixture Mage_Core_Model_Store */
    $storeFixture = require 'API/_fixture/_block/Core/Store.php';
    $storeFixture->setWebsiteId($defaultWebsite->getId())->setGroupId($defaultWebsite->getDefaultGroupId())->save();
    Magento_Test_TestCase_ApiAbstract::setFixture('store', $storeFixture);
    Mage::app()->reinitStores();
}
