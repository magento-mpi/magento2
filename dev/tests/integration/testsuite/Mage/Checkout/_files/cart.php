<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Checkout
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

Mage::unregister('_singleton/Mage_Checkout_Model_Session');
Mage::unregister('_singleton/Mage_Checkout_Model_Cart');
/** @var $cart Mage_Checkout_Model_Cart */
$cart = Mage::getSingleton('Mage_Checkout_Model_Cart');

$cart->addProduct($product, $requestInfo);
$cart->save();

$quoteItemId = $cart->getQuote()->getItemByProduct($product)->getId();
Mage::register('product/quoteItemId', $quoteItemId);
Mage::unregister('_singleton/Mage_Checkout_Model_Session');
Mage::unregister('_singleton/Mage_Checkout_Model_Cart');