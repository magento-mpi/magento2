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

require realpath(dirname(__FILE__) . '/../../..') . '/Api/SalesOrder/_fixtures/product_simple.php';
/** @var $product Mage_Catalog_Model_Product */
$product = Magento_Test_Webservice::getFixture('product_simple');

/** @var $review Mage_Review_Model_Review */
$review = new Mage_Review_Model_Review();
$reviewData = require 'Customer/ReviewData.php';
$reviewData['status_id'] = Mage_Review_Model_Review::STATUS_APPROVED;
$reviewData['stores'] = Mage::app()->getWebsite()->getStoreIds();
$review->setData($reviewData);
$entityId = $review->getEntityIdByCode(Mage_Review_Model_Review::ENTITY_PRODUCT_CODE);
/** @var $customerModel Mage_Customer_Model_Customer */
$customerModel = Mage::getModel('customer/customer');
$customerModel->setWebsiteId(Mage::app()->getWebsite()->getId())->loadByEmail(TESTS_CUSTOMER_EMAIL);
$customerId = $customerModel->getId();
$review->setEntityId($entityId)
    ->setCustomerId($customerId)
    ->setProductId($product->getId())
    ->setEntityPkValue($product->getId())
    ->setStoreId($product->getStoreId())
    ->setStatusId($reviewData['status_id'])
    ->save();

Magento_Test_Webservice::setFixture('review_customer', $review);
