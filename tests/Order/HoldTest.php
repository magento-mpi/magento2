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
class Order_HoldTest extends Mage_Selenium_TestCase
{

    /**
     * <p>Preconditions:</p>
     *
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
        $productData = $this->loadData('simple_product_for_order', NULL, array('general_name', 'general_sku'));
        //Steps
        $this->navigate('manage_products');
        $this->assertTrue($this->checkCurrentPage('manage_products'), $this->messages);
        $this->productHelper()->createProduct($productData);
        //Verifying
        $this->assertTrue($this->successMessage('success_saved_product'), $this->messages);

        return $productData['general_sku'];
    }

    /**
     * <p>Holding and unholding order after creation.</p>
     * <p>Steps:</p>
     * <p>1. Navigate to "Manage Orders" page;</p>
     * <p>2. Create new order for new customer;</p>
     * <p>3. Hold order;</p>
     * <p>Expected result:</p>
     * <p>Order is holded;</p>
     * <p>4. Unhold order;</p>
     * <p>Expected result:</p>
     * <p>Order is unholded;</p>
     *
     *
     * @depends createSimpleProduct
     * @dataProvider dataPaymentMethods
     * @test
     */
    public function holdAndUnholdPendingOrderViaOrderPage($payment, $simpleSku)
    {
        //Data
        $orderData = $this->loadData('order_newcustmoer_' . $payment . '_flatrate', array('filter_sku' => $simpleSku));
        //Steps
        $this->navigate('system_configuration');
        if ($payment != 'checkmoney') {
            $payment .= '_without_3Dsecure';
        }
        if ($payment == 'paypaldirectuk') {
            $this->systemConfigurationHelper()->configure('paypal_enable');
        }
        $this->systemConfigurationHelper()->configure($payment);
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->createOrder($orderData);
        //Verifying
        $this->assertTrue($this->successMessage('success_created_order'), $this->messages);
        //Steps
        $this->clickButton('hold');
        //Verifying
        $this->assertTrue($this->successMessage('success_hold_order'), $this->messages);
        //Steps
        $this->clickButton('unhold');
        //Verifying
        $this->assertTrue($this->successMessage('success_unhold_order'), $this->messages);
    }

    public function dataPaymentMethods()
    {
        return array(
            array('savedcc'),
            array('paypaldirectuk'),
            array('checkmoney'),
            array('authorizenet')
        );
    }

}
