<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

if (!PHPUnit_Framework_TestCase::getFixture('store')) {
    $defaultWebsite = Mage::app()->getWebsite();
    /** @var $storeFixture Mage_Core_Model_Store */
    $storeFixture = require '_fixture/_block/Core/Store.php';
    $storeFixture->setWebsiteId($defaultWebsite->getId())->setGroupId($defaultWebsite->getDefaultGroupId())->save();
    PHPUnit_Framework_TestCase::setFixture('store', $storeFixture);
    Mage::app()->reinitStores();
}
