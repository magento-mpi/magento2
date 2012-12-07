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
$rule->save();
Mage::getModel('Mage_CatalogRule_Model_Rule')->applyAll();

Magento_Test_Webservice::setFixture('catalog_price_rule', $rule, Magento_Test_Webservice::AUTO_TEAR_DOWN_DISABLED);
