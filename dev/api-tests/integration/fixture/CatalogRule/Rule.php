<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Core
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */


$rule = new Mage_CatalogRule_Model_Rule;
$rule->setName('Test Catalog Rule 50$ off')
    ->setIsActive(1)
    ->setWebsiteIds(array(Mage::app()->getDefaultStoreView()->getWebsiteId()))
    ->setCustomerGroupIds(array(Mage_Customer_Model_Group::NOT_LOGGED_IN_ID))
    ->setSimpleAction('by_fixed')
    ->setDiscountAmount(50)
    ->setSubIsEnable(0);

return $rule;
