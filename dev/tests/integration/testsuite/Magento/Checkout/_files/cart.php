<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $objectManager \Magento\TestFramework\ObjectManager */
$objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
$objectManager->get('Magento\Framework\Registry')->unregister('_singleton/Magento\Checkout\Model\Session');
$objectManager->get('Magento\Framework\Registry')->unregister('_singleton/Magento_Checkout_Model_Cart');
/** @var $cart \Magento\Checkout\Model\Cart */
$cart = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Checkout\Model\Cart');

$cart->addProduct($product, $requestInfo);
$cart->save();

$quoteItemId = $cart->getQuote()->getItemByProduct($product)->getId();
$objectManager->get('Magento\Framework\Registry')->register('product/quoteItemId', $quoteItemId);
$objectManager->get('Magento\Framework\Registry')->unregister('_singleton/Magento\Checkout\Model\Session');
$objectManager->get('Magento\Framework\Registry')->unregister('_singleton/Magento_Checkout_Model_Cart');
