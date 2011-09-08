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
class Paas_FrontendGeneralTest extends Mage_Selenium_TestCase
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
     * <p>Test navigation.</p>
     *
     * @test
     */
    public function navigation()
    {
        $this->frontend('home');

        $this->navigate('register_account');
        $this->assertTrue($this->checkCurrentPage('register_account'), 'Wrong page is opened');
        $this->assertTrue($this->controlIsPresent('link', 'back'), 'There is no "Back" button on the page');
        $this->assertTrue($this->buttonIsPresent('submit'), 'There is no "submit" button on the page');
    }

    /**
     * Create customer.
     *
     * @depends navigation
     * @test
     */
    public function createCustomer()
    {
        $this->frontend('home');

        $this->navigate('register_account');
        $this->assertTrue($this->checkCurrentPage('register_account'), 'Wrong page is opened');

        //Data
        $userData = $this->loadData('customer_account_register', NULL, 'email');

        //Fill in 'Account Information' tab
        $this->fillForm($userData, 'register_account');
        $this->saveForm('submit');

        //Verifying
        $this->assertTrue($this->successMessage('success_registration'), $this->messages);
        $this->assertTrue($this->checkCurrentPage('customer_account'),
                'After successful customer creation should be redirected to My Account page');

        return $userData;
    }

    /**
     * Create order.
     *
     * @depends createCustomer
     * @depends createSimpleProduct
     *
     * @test
     */
    public function createOrder($userData, $productData)
    {
//        $this->frontend('home');
//
//        $this->addParameter('productName', $productData["general_name"]);
//        $productSearchRequest = str_replace(' ', '+', $productData["general_name"]);
//        $this->addParameter('productSearchRequest', $productSearchRequest);
//
//        $this->fillForm((array)$productData["general_name"],'home');
//        $this->clickControl('button','search');

        $this->markTestIncomplete();
    }

}
