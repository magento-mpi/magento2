<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Creates simple Catalog Rule with the following data:
 * active, applied to all products, without time limits, with 10% off for Not Logged In Customers
 */

/** @var $banner Magento_CatalogRule_Model_Rule */
$catalogRule = Mage::getModel('Magento_CatalogRule_Model_Rule');

$catalogRule->setIsActive(1)
    ->setName('Test Catalog Rule')
    ->setCustomerGroupIds(Magento_Customer_Model_Group::NOT_LOGGED_IN_ID)
    ->setDiscountAmount(10)
    ->setWebsiteIds(array(0 => 1))
    ->setSimpleAction('by_percent')
    ->save();

$catalogRule->applyAll();
