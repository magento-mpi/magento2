<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Checkout
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $objectManager Magento_TestFramework_ObjectManager */
$objectManager = Magento_TestFramework_Helper_Bootstrap::getObjectManager();
$objectManager->get('Magento_Core_Model_Registry')->unregister('_singleton/Magento_Checkout_Model_Session');
$objectManager->get('Magento_Core_Model_Registry')->unregister('_singleton/Magento_Checkout_Model_Cart');
/** @var $cart Magento_Checkout_Model_Cart */
$cart = Magento_TestFramework_Helper_Bootstrap::getObjectManager()->get('Magento_Checkout_Model_Cart');

$cart->addProduct($product, $requestInfo);
$cart->save();

$quoteItemId = $cart->getQuote()->getItemByProduct($product)->getId();
$objectManager->get('Magento_Core_Model_Registry')->register('product/quoteItemId', $quoteItemId);
$objectManager->get('Magento_Core_Model_Registry')->unregister('_singleton/Magento_Checkout_Model_Session');
$objectManager->get('Magento_Core_Model_Registry')->unregister('_singleton/Magento_Checkout_Model_Cart');
