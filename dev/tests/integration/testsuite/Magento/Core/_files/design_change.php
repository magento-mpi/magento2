<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

$storeId = Mage::app()->getAnyStoreView()->getId();
/** @var $change Magento_Core_Model_Design */
$change = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Core_Model_Design');
$change->setStoreId($storeId)
    ->setDesign('magento_blank')
    ->setDateFrom('2001-01-01 01:01:01')
    ->save(); // creating with predefined ID doesn't work for some reason
