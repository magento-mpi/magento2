<?php
/**
 * Fixture roll back logic.
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */

/** @var $objectManager Magento_Test_ObjectManager */
$objectManager = Magento_Test_Helper_Bootstrap::getObjectManager();
$objectManager->get('Magento_Core_Model_Registry')->unregister('quote');
$objectManager->get('Magento_Core_Model_Registry')->unregister('order');
$objectManager->get('Magento_Core_Model_Registry')->unregister('product_simple');
$objectManager->get('Magento_Core_Model_Registry')->unregister('customer');
$objectManager->get('Magento_Core_Model_Registry')->unregister('customer_address');
$objectManager->get('Magento_Core_Model_Registry')->unregister('shipment');
