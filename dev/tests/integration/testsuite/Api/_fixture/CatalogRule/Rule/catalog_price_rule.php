<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/* @var $ruleFixture Mage_CatalogRule_Model_Rule */
$ruleFixture = require 'API/_fixture/_block/CatalogRule/Rule.php';

$rule = clone $ruleFixture;
$rule->save();
Mage::getModel('Mage_CatalogRule_Model_Rule')->applyAll();

Magento_Test_TestCase_ApiAbstract::setFixture(
    'catalog_price_rule',
    $rule,
    Magento_Test_TestCase_ApiAbstract::AUTO_TEAR_DOWN_DISABLED
);
