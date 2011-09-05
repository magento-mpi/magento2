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
 * @TODO
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
        $this->navigate('manage_products');
        $this->assertTrue($this->checkCurrentPage('manage_products'), 'Wrong page is opened');
        $this->addParameter('id', '0');
    }
    /**
     * <p>Holding order after creation via mass action.</p>
     * <p>Steps:</p>
     * <p>1. Navigate to "Manage Orders" page;</p>
     * <p>2. Create new order for new customer;</p>
     * <p>3. Hold order via mass action menu;</p>
     * <p>Expected result:</p>
     * <p>Order is holded;</p>
     *
     * @test
     */
    public function holdViaMassAction()
    {
        $productData = $this->loadData('simple_product_for_order', null, array('general_name', 'general_sku'));
        $this->productHelper()->createProduct($productData);
        $this->assertTrue($this->successMessage('success_saved_product'), $this->messages);
        $this->assertTrue($this->checkCurrentPage('manage_products'),
                'After successful product creation should be redirected to Manage Products page');
        $billingAddress = $this->loadData('new_customer_order_billing_address_reqfields',
                array(
                    $this->orderHelper()->customerAddressGenerator(
                    ':alnum:', $addrType = 'billing', $symNum = 32, TRUE),
                    'billing_save_in_address_book' => 'yes' ));
        $billingAddress['email'] = $this->generate('email', 32, 'valid');
        $shippingAddress = array(
                'shipping_first_name'           => $billingAddress['billing_first_name'],
                'shipping_last_name'            => $billingAddress['billing_last_name'],
                'shipping_street_address_1'     => $billingAddress['billing_street_address_1'],
                'shipping_city'                 => $billingAddress['billing_city'],
                'shipping_zip_code'             => $billingAddress['billing_zip_code'],
                'shipping_telephone'            => $billingAddress['billing_telephone'],
                'shipping_save_in_address_book' => 'yes');
        $products = $this->loadData('simple_products_to_add');
        $products['product_1']['general_sku'] = $productData['general_sku'];
        $this->navigate('manage_sales_orders');
        $orderId = $this->orderHelper()->createOrderForNewCustomer(false, 'Default Store View',
                $products, $billingAddress['email'],
                $billingAddress, $shippingAddress, 'visa','Fixed');

        $this->assertTrue($this->navigate('manage_sales_orders'),
                'Could not get to Manage Sales Orders page');
        $this->searchAndChoose(array('1' => $orderId), 'sales_order_grid');
        $userData = array('actions' => 'Hold');
        $this->fillForm($userData, 'sales_order_grid');
        $this->_currentPage = $this->_findCurrentPageFromUrl($this->getLocation());
        $this->clickButton('submit');
        $this->assertTrue($this->successMessage('success_hold_order'), $this->messages);
        return $orderId;
    }

    /**
     * <p>Unholding order after holding via mass action menu.</p>
     * <p>Steps:</p>
     * <p>1. Navigate to "Manage Orders" page;</p>
     * <p>2. Choose already holded order;</p>
     * <p>3. Unhold order via mass action menu;</p>
     * <p>Expected result:</p>
     * <p>Order is unholded;</p>
     *
     * @depends holdViaMassAction
     * @test
     */
    public function unholdViaMassAction($orderId)
    {
        $this->navigate('manage_sales_orders');
        $this->assertTrue($this->navigate('manage_sales_orders'),
                'Could not get to Manage Sales Orders page');
        $this->searchAndChoose(array('1' => $orderId), 'sales_order_grid');
        $userData = array('actions' => 'Unhold');
        $this->fillForm($userData, 'sales_order_grid');
        $this->_currentPage = $this->_findCurrentPageFromUrl($this->getLocation());
        $this->clickButton('submit');
        $this->assertTrue($this->successMessage('success_unhold_order'), $this->messages);
    }

    /**
     * <p>Holding order after creation.</p>
     * <p>Steps:</p>
     * <p>1. Navigate to "Manage Orders" page;</p>
     * <p>2. Create new order for new customer;</p>
     * <p>3. Hold order;</p>
     * <p>Expected result:</p>
     * <p>Order is holded;</p>
     *
     * @test
     */
    public function holdViaOrderView()
    {
        $productData = $this->loadData('simple_product_for_order', null, array('general_name', 'general_sku'));
        $this->productHelper()->createProduct($productData);
        $this->assertTrue($this->successMessage('success_saved_product'), $this->messages);
        $this->assertTrue($this->checkCurrentPage('manage_products'),
                'After successful product creation should be redirected to Manage Products page');
        $billingAddress = $this->loadData('new_customer_order_billing_address_reqfields',
                array(
                    $this->orderHelper()->customerAddressGenerator(
                    ':alnum:', $addrType = 'billing', $symNum = 32, TRUE),
                    'billing_save_in_address_book' => 'yes' ));
        $billingAddress['email'] = $this->generate('email', 32, 'valid');
        $shippingAddress = array(
                'shipping_first_name'           => $billingAddress['billing_first_name'],
                'shipping_last_name'            => $billingAddress['billing_last_name'],
                'shipping_street_address_1'     => $billingAddress['billing_street_address_1'],
                'shipping_city'                 => $billingAddress['billing_city'],
                'shipping_zip_code'             => $billingAddress['billing_zip_code'],
                'shipping_telephone'            => $billingAddress['billing_telephone'],
                'shipping_save_in_address_book' => 'yes');
        $products = $this->loadData('simple_products_to_add');
        $products['product_1']['general_sku'] = $productData['general_sku'];
        $this->navigate('manage_sales_orders');
        $orderId = $this->orderHelper()->createOrderForNewCustomer(false, 'Default Store View',
                $products, $billingAddress['email'],
                $billingAddress, $shippingAddress, 'visa','Fixed');

        $this->assertTrue($this->navigate('manage_sales_orders'),
                'Could not get to Manage Sales Orders page');
        $this->searchAndOpen(array('1' => $orderId), TRUE, 'sales_order_grid');
        $this->clickButton('hold', TRUE);
        $this->defineIdFromUrl();
        $this->assertTrue($this->successMessage('success_hold_order'), $this->messages);
        return $orderId;
    }

    /**
     * <p>Unholding order after holding.</p>
     * <p>Steps:</p>
     * <p>1. Navigate to "Manage Orders" page;</p>
     * <p>2. Choose already holded order;</p>
     * <p>3. Unhold order;</p>
     * <p>Expected result:</p>
     * <p>Order is unholded;</p>
     *
     * @depends holdViaOrderView
     * @test
     */
    public function unholdViaOrderView($orderId)
    {
        $this->navigate('manage_sales_orders');
        $this->assertTrue($this->navigate('manage_sales_orders'),
                'Could not get to Manage Sales Orders page');
        $this->searchAndOpen(array('1' => $orderId), TRUE, 'sales_order_grid');
        $this->_currentPage = $this->_findCurrentPageFromUrl($this->getLocation());
        $this->clickButton('unhold', TRUE);
        $this->defineIdFromUrl();
        $this->assertTrue($this->successMessage('success_unhold_order'), $this->messages);
    }
}
