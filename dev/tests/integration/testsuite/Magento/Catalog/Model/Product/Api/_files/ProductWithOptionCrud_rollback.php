<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */

/** @var $objectManager Magento_TestFramework_ObjectManager */
$objectManager = Magento_TestFramework_Helper_Bootstrap::getObjectManager();
$objectManager->get('Magento_Core_Model_Registry')->unregister('attributes');
$objectManager->get('Magento_Core_Model_Registry')->unregister('optionValueApi');
$objectManager->get('Magento_Core_Model_Registry')->unregister('optionValueInstaller');
