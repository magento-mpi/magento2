<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */

/** @var $objectManager Magento_Test_ObjectManager */
$objectManager = Magento_Test_Helper_Bootstrap::getObjectManager();
$objectManager->get('Magento_Core_Model_Registry')->unregister('quote');
$objectManager->get('Magento_Core_Model_Registry')->unregister('order');
$objectManager->get('Magento_Core_Model_Registry')->unregister('quote2');
$objectManager->get('Magento_Core_Model_Registry')->unregister('order2');
