<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Wishlist
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

require __DIR__ . '/../../../Magento/Customer/_files/customer.php';
require __DIR__ . '/../../../Magento/Catalog/_files/product_simple.php';

$wishlist = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create('Magento\Wishlist\Model\Wishlist');
$wishlist->loadByCustomer($customer->getId(), true);
$item = $wishlist->addNewItem($product, new \Magento\Object(array()));
$wishlist->setSharingCode('fixture_unique_code')->save();
