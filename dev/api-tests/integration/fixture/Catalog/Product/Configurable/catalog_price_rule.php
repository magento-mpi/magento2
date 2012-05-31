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

/* @var $ruleFixture Mage_CatalogRule_Model_Rule */
$ruleFixture = require TEST_FIXTURE_DIR . '/_block/CatalogRule/Rule.php';

$rule = clone $ruleFixture;
// create catalog price rule for configurable options
$rule->setSubIsEnable(1)
    ->setWebsiteIds(array(0, Mage::app()->getDefaultStoreView()->getWebsiteId()))
    ->setCustomerGroupIds(array(Mage_Customer_Model_Group::NOT_LOGGED_IN_ID, 1))
    ->setSubSimpleAction('by_fixed')
    ->setDiscountAmount(0.5)
    ->setSubDiscountAmount(0.15)
    ->save();
Mage::getModel('Mage_CatalogRule_Model_Rule')->applyAll();

Magento_Test_Webservice::setFixture('catalog_price_rule', $rule, Magento_Test_Webservice::AUTO_TEAR_DOWN_DISABLED);
