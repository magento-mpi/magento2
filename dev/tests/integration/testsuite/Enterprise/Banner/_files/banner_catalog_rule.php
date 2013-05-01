<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

//this fixture adds existing catalog rule to banner from banner.php

require __DIR__ . '/banner.php';

$catalogRule = Mage::getModel('Mage_CatalogRule_Model_Rule');
$ruleId = $catalogRule->getCollection()->getFirstItem()->getId();

$banner->setBannerCatalogRules(array($ruleId))->save();

