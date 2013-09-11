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

Mage::unregister('_singleton/\Magento\Checkout\Model\Session');
Mage::unregister('_singleton/\Magento\Checkout\Model\Cart');
/** @var $cart \Magento\Checkout\Model\Cart */
$cart = Mage::getSingleton('Magento\Checkout\Model\Cart');

$cart->addProduct($product, $requestInfo);
$cart->save();

$quoteItemId = $cart->getQuote()->getItemByProduct($product)->getId();
Mage::register('product/quoteItemId', $quoteItemId);
Mage::unregister('_singleton/\Magento\Checkout\Model\Session');
Mage::unregister('_singleton/\Magento\Checkout\Model\Cart');