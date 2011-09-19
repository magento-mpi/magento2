<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Paas
 * @package     tests
 * @subpackage  integration_tests
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

//Add customer
$tagFixture = simplexml_load_file(dirname(__FILE__).'/xml/tagcrud.xml');
$customerData = Magento_Test_Webservice::simpleXmlToArray($tagFixture->customer);
$customerData['email'] = mt_rand(1000, 9999) . '.' . $customerData['email']; 

$customer = new Mage_Customer_Model_Customer();
$customer->setData($customerData)->save();
Magento_Test_Webservice::setFixture('customerData', $customer);


//Create new simple product
$productData = Magento_Test_Webservice::simpleXmlToArray($tagFixture->product);
$productData['sku'] = $productData['sku'] . mt_rand(1000, 9999); 
$productData['name'] = $productData['name'] . ' ' . mt_rand(1000, 9999); 

$product = new Mage_Catalog_Model_Product();
$product->setData($productData)->save();
Magento_Test_Webservice::setFixture('productData', $product);
