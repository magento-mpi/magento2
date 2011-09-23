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
 * Reorder Test
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Order_ReorderTest extends Mage_Selenium_TestCase
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
     * <p>TL-MAGE-321:Reorder.</p>
     * <p>Steps:</p>
     * <p>1.Go to Sales-Orders;</p>
     * <p>2.Press "Create New Order" button;</p>
     * <p>3.Press "Create New Customer" button;</p>
     * <p>4.Choose 'Main Store' (First from the list of radiobuttons) if exists;</p>
     * <p>5.Fill all required fields;</p>
     * <p>6.Press 'Add Products' button;</p>
     * <p>7.Add products;</p>
     * <p>8.Choose shipping address the same as billing;</p>
     * <p>9.Check payment method 'Credit Card';</p>
     * <p>10.Choose any from 'Get shipping methods and rates';</p>
     * <p>11. Submit order;</p>
     * <p>12. Edit order (add products and change billing address);</p>
     * <p>13. Submit order;</p>
     * <p>Expected results:</p>
     * <p>New customer successfully created. Order is created for the new customer;</p>
     * <p>Message "The order has been created." is displayed.</p>
     * <p>New order during reorder is created.</p>
     * <p>Message "The order has been created." is displayed.</p>
     *
     * @depends createSimpleProduct
     * @dataProvider dataPaymentMethods
     * @test
     */
    public function reorderPendingOrder($payment, $simpleSku)
    {
        //Data
        $errors = array();
        $orderData = $this->loadData('order_newcustmoer_' . $payment . '_flatrate', array('filter_sku' => $simpleSku));
        //Steps
        $this->navigate('system_configuration');
        if ($payment != 'checkmoney') {
            $payment .= '_without_3Dsecure';
        }
        if ($payment == 'paypaldirect' || $payment == 'paypaldirectuk' || $payment == 'payflowpro') {
            $this->systemConfigurationHelper()->configure('paypal_enable');
        }
        $this->systemConfigurationHelper()->configure($payment);
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->createOrder($orderData);
        //Verifying
        $this->assertTrue($this->successMessage('success_created_order'), $this->messages);
        //Steps
        $this->clickButton('reorder');
        if (isset($orderData['payment_data']['payment_info'])) {
            $data = $orderData['payment_data']['payment_info'];
            $emptyFields = array('card_number', 'card_verification_number');
            foreach ($emptyFields as $field) {
                $xpath = $this->_getControlXpath('field', $field);
                $value = $this->getAttribute($xpath . '@value');
                if ($value) {
                    $errors[] = "Value for field '$field' should be empty, but now is $value";
                }
            }
            $this->fillForm(array('card_number' => $data['card_number'],
                'card_verification_number' => $data['card_verification_number']));
        }
        $this->saveForm('submit_order');
        //Verifying
        $this->assertTrue($this->successMessage('success_created_order'), $this->messages);
        if ($errors) {
            $this->fail(implode("\n", $errors));
        }
    }

    public function dataPaymentMethods()
    {
        return array(
            array('paypaldirect'),
            array('savedcc'),
            array('paypaldirectuk'),
            array('checkmoney'),
            array('payflowpro'),
            array('authorizenet')
        );
    }

}
