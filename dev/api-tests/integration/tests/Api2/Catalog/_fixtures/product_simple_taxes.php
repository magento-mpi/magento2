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
$product->addData($productData)
    ->setStoreId(0)
    ->setPrice(10)
    ->setTaxClassId(2)
    ->setStockData(array('use_config_manage_stock' => 1))
    ->setWebsiteIds(array(Mage::app()->getDefaultStoreView()->getWebsiteId()))
    ->save();

Magento_Test_Webservice::setFixture('product_simple_taxes', $product);
