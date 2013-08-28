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

Mage::unregister('_singleton/Magento_Checkout_Model_Session');
Mage::unregister('_singleton/Magento_Checkout_Model_Cart');
/** @var $cart Magento_Checkout_Model_Cart */
$cart = Mage::getSingleton('Magento_Checkout_Model_Cart');

$cart->addProduct($product, $requestInfo);
$cart->save();

$quoteItemId = $cart->getQuote()->getItemByProduct($product)->getId();
Mage::register('product/quoteItemId', $quoteItemId);
Mage::unregister('_singleton/Magento_Checkout_Model_Session');
Mage::unregister('_singleton/Magento_Checkout_Model_Cart');