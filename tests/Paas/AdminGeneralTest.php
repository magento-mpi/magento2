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
 * @category    tests
 * @package     selenium
 * @subpackage  tests
 * @author      Magento Core Team <core@magentocommerce.com>
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Test creation new customer from Backend
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Paas_AdminGeneralTest extends Mage_Selenium_TestCase
{

    /**
     * Create simple product
     *
     * @test
     */
    public function createSimpleProduct()
    {
        $this->loginAdminUser();

        $this->navigate('manage_products');
        $this->assertTrue($this->checkCurrentPage('manage_products'), 'Wrong page is opened');
        $this->addParameter('id', '0');

        //Data
        $productData = $this->loadData('simple_product_required', null, array('general_name', 'general_sku'));
        //Steps
        $this->productHelper()->createProduct($productData);
        //Verifying
        $this->assertTrue($this->successMessage('success_saved_product'), $this->messages);
        $this->assertTrue($this->checkCurrentPage('manage_products'),
                'After successful product creation should be redirected to Manage Products page');

        return $productData;
    }

    /**
     * Create customer.
     *
     * @test
     */
    public function createCustomer()
    {
        $this->loginAdminUser();

        $this->navigate('manage_customers');
        $this->assertTrue($this->checkCurrentPage('manage_customers'), 'Wrong page is opened');
        $this->addParameter('id', '0');

        //Data
        $userData = $this->loadData('generic_customer_account', NULL, 'email');
        $addressData = $this->loadData('new_customer_address');
        //Steps
        $this->CustomerHelper()->createCustomer($userData, $addressData);
        //Verifying
        $this->assertTrue($this->successMessage('success_saved_customer'), $this->messages);
        $this->assertTrue($this->checkCurrentPage('manage_customers'),
                'After successful customer creation should be redirected to Manage Customers page');

        $userDataAddress = array_merge($userData, $addressData);

        return $userDataAddress;
    }

    /**
     * Create order.
     *
     * @depends createCustomer
     * @depends createSimpleProduct
     *
     * @test
     */
    public function createOrder($userDataAddress, $productData)
    {
        (array) $prodToAdd = array('product_name' => $productData["general_name"]);

        $orderId = $this->OrderHelper()->createOrderForExistingCustomer(true, 'Default Store View', $prodToAdd,
                $userDataAddress["email"], $userDataAddress, $userDataAddress, 'visa', 'Fixed');

        return $orderId;
    }

    /**
     * Sync orders test
     *
     * @depends createOrder
     *
     * @test
     */
    public function syncOrders($orderId)
    {
        // @TODO
        // $this->PaasHelper()->sync();
        return $orderId;
    }

    /**
     * Search sync order
     *
     * @depends syncOrders
     *
     * @test
     */
    public function searchSyncOrders($orderId)
    {
        $this->_applicationHelper->changeAppInfo($configName);
        $this->loginAdminUser();
    }

}
