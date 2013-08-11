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

require __DIR__ . '/../../../Magento/Catalog/_files/products.php';

/** @var $quote Mage_Sales_Model_Quote */
$quote = Mage::getModel('Mage_Sales_Model_Quote');
$quote->setStoreId(1)
    ->setIsActive(false)
    ->setIsMultiShipping(false)
    ->addProduct($product->load($product->getId()), 2);

$quote->getPayment()->setMethod('checkmo');

$quote->collectTotals();
$quote->save();

$quoteService = new Mage_Sales_Model_Service_Quote($quote);
$quoteService->getQuote()->getPayment()->setMethod('checkmo');
