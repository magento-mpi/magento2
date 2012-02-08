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
require realpath(dirname(__FILE__) . '/../../..') . '/Api/SalesOrder/_fixtures/product_virtual.php';
/** @var $productSimple Mage_Catalog_Model_Product */
$productSimple = Magento_Test_Webservice::getFixture('product_simple');
/** @var $productVirtual Mage_Catalog_Model_Product */
$productVirtual = Magento_Test_Webservice::getFixture('product_virtual');

$reviewsList = array();

/** @var $review Mage_Review_Model_Review */
$review = new Mage_Review_Model_Review();
$reviewData = require 'ReviewData.php';
$review->setData($reviewData);
$entityId = $review->getEntityIdByCode(Mage_Review_Model_Review::ENTITY_PRODUCT_CODE);

// Review #1: Simple Product, Status Approved
$review->setEntityId($entityId)
    ->setEntityPkValue($productSimple->getId())
    ->setStoreId($productSimple->getStoreId())
    ->setStatusId(Mage_Review_Model_Review::STATUS_APPROVED)
    ->save();
$reviewsList[] = $review;

/** @var $customerModel Mage_Customer_Model_Customer */
$customerModel = Mage::getModel('customer/customer');
$customerModel->setWebsiteId(Mage::app()->getWebsite()->getId())->loadByEmail(TESTS_CUSTOMER_EMAIL);
$customerId = $customerModel->getId();

// Review #2: Simple Product, Status Pending
$review2 = new Mage_Review_Model_Review();
$review2->setData($reviewData)
    ->setCustomerId($customerId)
    ->setEntityId($entityId)
    ->setEntityPkValue($productSimple->getId())
    ->setStoreId($productSimple->getStoreId())
    ->setStatusId(Mage_Review_Model_Review::STATUS_PENDING)
    ->save();
$reviewsList[] = $review2;

// Review #3: Virtual Product, Status Approved
$review3 = new Mage_Review_Model_Review();
$review3->setData($reviewData)
    ->setCustomerId($customerId)
    ->setEntityId($entityId)
    ->setEntityPkValue($productVirtual->getId())
    ->setStoreId($productVirtual->getStoreId())
    ->setStatusId(Mage_Review_Model_Review::STATUS_APPROVED)
    ->save();
$reviewsList[] = $review3;

Magento_Test_Webservice::setFixture('reviews_list', $reviewsList);
