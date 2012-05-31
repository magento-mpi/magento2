<?php
/**
 * {license_notice}
 *
 * @category    Paas
 * @package     tests
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

if (!Magento_Test_Webservice::getFixture('store')) {
    $defaultWebsite = Mage::app()->getWebsite();
    /** @var $storeFixture Mage_Core_Model_Store */
    $storeFixture = require TESTS_FIXTURES_DIRECTORY . '/_block/Core/Store.php';
    $storeFixture->setWebsiteId($defaultWebsite->getId())->setGroupId($defaultWebsite->getDefaultGroupId())->save();
    Magento_Test_Webservice::setFixture('store', $storeFixture);
    Mage::app()->reinitStores();
}
