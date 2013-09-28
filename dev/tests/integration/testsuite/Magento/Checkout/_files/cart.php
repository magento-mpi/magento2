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

/** @var $objectManager \Magento\TestFramework\ObjectManager */
$objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
$objectManager->get('Magento\Core\Model\Registry')->unregister('_singleton/Magento\Checkout\Model\Session');
$objectManager->get('Magento\Core\Model\Registry')->unregister('_singleton/Magento_Checkout_Model_Cart');
/** @var $cart \Magento\Checkout\Model\Cart */
$cart = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Checkout\Model\Cart');

$cart->addProduct($product, $requestInfo);
$cart->save();

$quoteItemId = $cart->getQuote()->getItemByProduct($product)->getId();
$objectManager->get('Magento\Core\Model\Registry')->register('product/quoteItemId', $quoteItemId);
$objectManager->get('Magento\Core\Model\Registry')->unregister('_singleton/Magento\Checkout\Model\Session');
$objectManager->get('Magento\Core\Model\Registry')->unregister('_singleton/Magento_Checkout_Model_Cart');
