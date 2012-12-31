<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/* @var $ruleFixture Mage_CatalogRule_Model_Rule */
$ruleFixture = require '_fixture/_block/CatalogRule/Rule.php';

$rule = clone $ruleFixture;
$rule->save();
Mage::getModel('Mage_CatalogRule_Model_Rule')->applyAll();

PHPUnit_Framework_TestCase::setFixture(
    'catalog_price_rule',
    $rule,
    PHPUnit_Framework_TestCase::AUTO_TEAR_DOWN_DISABLED
);
