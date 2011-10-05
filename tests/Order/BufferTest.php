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
 * @Test Hold and Unhold
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Order_BufferTest extends Mage_Selenium_TestCase
{

    /**
     * <p>Preconditions:</p>
     * <p>Log in to Backend.</p>
     */
    public function setUpBeforeTests()
    {
        $this->loginAdminUser();
    }

    protected function assertPreConditions()
    {
        $this->addParameter('id', '0');
    }

    /**
     * Create Simple Product for tests
     *
     * @test
     */
    public function createSimpleProduct()
    {
        //Data
        $simpleSku = $this->loadData('simple_product_for_order_prices_validation',
                NULL, array('general_name', 'general_sku'));
        //Steps
        $this->navigate('manage_products');
        $this->assertTrue($this->checkCurrentPage('manage_products'), $this->messages);
        $this->productHelper()->createProduct($simpleSku);
        //Verifying
        $this->assertTrue($this->successMessage('success_saved_product'), $this->messages);

        return $simpleSku;
    }

    /**
     * @depends createSimpleProduct
     * @test
     */
    public function newCustomerWithoutAddress($simpleSku)
    {
        //Data
        $orderData = $this->loadData('order_for_validating_prices',
                array('filter_sku' => $simpleSku['general_sku'],
                    'customer_email' => $this->generate('email', 32, 'valid')));
        $verificationData = $this->loadData('validate_prices_during_order_creation',
                array('product_name' => $simpleSku['general_name']));
        //Steps
        $this->navigate('manage_sales_orders');
        //NOTE: You need to turn off pressing 'Submit Order' button in helper for running this test
        $this->orderHelper()->createOrder($orderData);
        $this->orderHelper()->verifyPrices($verificationData);

    }

}
