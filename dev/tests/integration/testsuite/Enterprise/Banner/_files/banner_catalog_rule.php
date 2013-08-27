<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adds existing catalog rule to banner
 */

require __DIR__ . '/banner.php';
require __DIR__ . '/../../../Magento/CatalogRule/_files/catalog_rule_10_off_not_logged.php';

$catalogRule = Mage::getModel('Magento_CatalogRule_Model_Rule');
$ruleId = $catalogRule->getCollection()->getFirstItem()->getId();

$banner = Mage::getModel('Enterprise_Banner_Model_Banner');
$banner->load('Test Banner', 'name')->setBannerCatalogRules(array($ruleId))->save();

