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
 * Creating order for new customer with one required field empty
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Order_Create_WithDifferentCreditCardTest extends Mage_Selenium_TestCase
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
    public function createSimple()
    {
        //Data
        $productData = $this->loadData('simple_product_for_order', NULL, array('general_name', 'general_sku'));
        //Steps
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($productData);
        //Verifying
        $this->assertTrue($this->successMessage('success_saved_product'), $this->messages);

        return $productData['general_sku'];
    }

    /**
     * Create order with Saved CC using all types of credit card
     *
     * @param type $simpleSku
     *
     * @depends createSimple
     * @dataProvider dataCardSavedCC
     * @test
     */
    public function differentCardInSavedCC($card, $simpleSku)
    {
        //Data
        $orderData = $this->loadData('order_newcustmoer_savedcc_flatrate', array('filter_sku' => $simpleSku));
        $orderData['payment_data']['payment_info'] = $this->loadData($card);
        //Steps
//        $this->navigate('system_configuration');
//        $this->systemConfigurationHelper()->configure('savedcc_without_3Dsecure');
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->createOrder($orderData);
        //Verifying
        $this->assertTrue($this->successMessage('success_created_order'), $this->messages);
    }

    public function dataCardSavedCC()
    {
        return array(
            array('saved_american_express'),
            array('saved_visa'),
            array('saved_mastercard'),
            array('saved_jcb'),
            array('saved_discover'),
            array('saved_solo'),
            array('saved_other'),
//            array('saved_enroute'),
//            array('saved_laser'),
//            array('saved_uatp'),
//            array('saved_diners_club'),
//            array('saved_switch_maestro')
        );
    }

    /**
     * Create order with AuthorizeNet using all types of credit card
     *
     * @param type $simpleSku
     *
     * @depends createSimple
     * @dataProvider dataCardAuthorizeNet
     * @test
     */
    public function differentCardInAuthorizeNet($card, $simpleSku)
    {
        //Data
        $orderData = $this->loadData('order_newcustmoer_authorizenet_flatrate', array('filter_sku' => $simpleSku));
        $orderData['payment_data']['payment_info'] = $this->loadData($card);
        //Steps
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('authorizenet_without_3Dsecure');
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->createOrder($orderData);
        //Verifying
        $this->assertTrue($this->successMessage('success_created_order'), $this->messages);
    }

    public function dataCardAuthorizeNet()
    {
        return array(
            array('else_american_express'),
            array('else_visa'),
            array('else_mastercard'),
            array('else_discover'),
            array('else_other')
        );
    }

    /**
     * Create order with PayFlowPro Verisign using all types of credit card
     *
     * @param type $simpleSku
     *
     * @depends createSimple
     * @dataProvider dataCardPayFlowProVerisign
     * @test
     */
    public function differentCardInPayFlowProVerisign($card, $simpleSku)
    {
        //Data
        $orderData = $this->loadData('order_newcustmoer_payflowpro_flatrate', array('filter_sku' => $simpleSku));
        $orderData['payment_data']['payment_info'] = $this->loadData($card);
        //Steps
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('paypal_enable');
        $this->systemConfigurationHelper()->configure('payflowpro_without_3Dsecure');
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->createOrder($orderData);
        //Verifying
        $this->assertTrue($this->successMessage('success_created_order'), $this->messages);
    }

    public function dataCardPayFlowProVerisign()
    {
        return array(
            array('else_american_express'),
            array('else_visa'),
            array('else_mastercard'),
            array('else_discover'),
            array('else_jcb')
        );
    }

    /**
     * Create order with PayPal Direct using all types of credit card
     *
     * @param type $simpleSku
     *
     * @depends createSimple
     * @dataProvider dataCardPayPalDirect
     * @test
     */
    public function differentCardInPayPalDirect($card, $simpleSku)
    {
        //Data
        $orderData = $this->loadData('order_newcustmoer_paypaldirect_flatrate', array('filter_sku' => $simpleSku));
        $orderData['payment_data']['payment_info'] = $this->loadData($card);
        //Steps
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('paypal_enable');
        $this->systemConfigurationHelper()->configure('paypaldirect_without_3Dsecure');
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->createOrder($orderData);
        //Verifying
        $this->assertTrue($this->successMessage('success_created_order'), $this->messages);
    }

    public function dataCardPayPalDirect()
    {
        return array(
            array('else_american_express'),
            array('else_visa'),
            array('else_mastercard'),
            array('else_discover'),
            array('else_solo'),
            array('else_switch_maestro')
        );
    }

    /**
     * Create order with PayPal Direct Uk using all types of credit card
     *
     * @param type $simpleSku
     *
     * @depends createSimple
     * @dataProvider dataCardPayPalDirectUk
     * @test
     */
    public function differentCardInPayPalDirectUk($card, $simpleSku)
    {
        //Data
        $orderData = $this->loadData('order_newcustmoer_paypaldirectuk_flatrate', array('filter_sku' => $simpleSku));
        $orderData['payment_data']['payment_info'] = $this->loadData($card);
        //Steps
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('paypal_enable');
        $this->systemConfigurationHelper()->configure('paypaldirectuk_without_3Dsecure');
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->createOrder($orderData);
        //Verifying
        $this->assertTrue($this->successMessage('success_created_order'), $this->messages);
    }

    public function dataCardPayPalDirectUk()
    {
        return array(
            array('else_american_express'),
            array('else_visa'),
            array('else_mastercard'),
            array('else_other'),
            array('else_solo'),
            array('else_switch_maestro')
        );
    }

}
