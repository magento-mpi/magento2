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
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Magento
 * @package     Mage_Core
 * @subpackage  integration_tests
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magento.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
$productData = require dirname(__FILE__) . '/Backend/SimpleProductData.php';
$product = new Mage_Catalog_Model_Product();
$product->setStoreId(0)
    ->setStockData(array(
        'use_config_manage_stock' => 0,
        'manage_stock' => 1,
        'qty' => 500,
        'is_qty_decimal' => 0,
        'is_in_stock' => 1,
    ))
    ->setTierPrice(
        array(
            array(
                'website_id' => 0,
                'cust_group' => Mage_Customer_Model_Group::CUST_GROUP_ALL,
                'price_qty' => 2,
                'price' => 95,
            ),
            array(
                'website_id' => 0,
                'cust_group' => 1, // General customer group
                'price_qty' => 5,
                'price' => 90,
            ),
            array(
                'website_id' => 0,
                'cust_group' => 0, // Not logged in customer group
                'price_qty' => 5,
                'price' => 93,
            ),
        )
    )
    ->setWebsiteIds(array(Mage::app()->getDefaultStoreView()->getWebsiteId()));

$product->addData($productData)->save();

// to make stock item visible from created product it should be reloaded
$product = Mage::getModel('catalog/product')->load($product->getId());
Magento_Test_Webservice::setFixture('product_simple', $product);
