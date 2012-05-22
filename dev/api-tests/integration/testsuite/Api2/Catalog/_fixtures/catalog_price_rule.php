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

$fixturesDir = realpath(dirname(__FILE__) . '/../../../../fixtures');

/* @var $ruleFixture Mage_CatalogRule_Model_Rule */
$ruleFixture = require $fixturesDir . '/CatalogRule/Rule.php';

$rule = clone $ruleFixture;
$rule->save();
Mage::getModel('Mage_Catalogrule_Model_Rule')->applyAll();

Magento_Test_Webservice::setFixture('catalog_price_rule', $rule);
