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

$wishlist = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Wishlist_Model_Wishlist');
$wishlist->loadByCustomer($customer->getId(), true);
$item = $wishlist->addNewItem($product, new Magento_Object(array(
//    'product' => '1',
//    'related_product' => '',
//    'options' => array(
//        1 => '1-text',
//        2 => array('month' => 1, 'day' => 1, 'year' => 2001, 'hour' => 1, 'minute' => 1),
//        3 => '1',
//        4 => '1',
//    ),
//    'validate_datetime_2' => '',
//    'qty' => '1',
)));
$wishlist->setSharingCode('fixture_unique_code')->save();
