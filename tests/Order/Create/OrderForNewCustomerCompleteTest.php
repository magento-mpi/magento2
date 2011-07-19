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
 * Creating order for new customer
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class OrderForNewCustomerComplete_Test extends Mage_Selenium_TestCase
{
   /**
    * Preconditions:
    *
    * Log in to Backend.
    *
    */
   public function setUpBeforeTests()
    {
        $this->windowMaximize();
        $this->loginAdminUser();
        $this->OrderHelper()->createProducts('product_to_order1', TRUE);
        $this->OrderHelper()->createProducts('product_to_order2', TRUE);
    }
   /**
    *
    * Create products for testing purposes.
    *
    * Navigate to Sales-Orders page.
    *
    */
    protected function assertPreConditions()
    {
        $this->addParameter('id', '0');
    }
   /**
    * Create customer via 'Create order' form (all fields are filled with special chars).
    * Create order(all fields are filled).
    *
    * Steps:
    *
    * 1.Go to Sales-Orders.
    *
    * 2.Press "Create New Order" button.
    *
    * 3.Press "Create New Customer" button.
    *
    * 4.Choose 'Main Store' (First from the list of radiobuttons) if exists.
    *
    * 5.Fill all fields with special characters.
    *
    * 6.Press 'Add Products' button.
    *
    * 7.Add first two products.
    *
    * 8.Choose shipping address the same as billing.
    *
    * 9.Check payment method 'Check / Money order'
    *
    * 10.Choose first from 'Get shipping methods and rates'.
    *
    * 11.Submit order.
    *
    * 12.Invoice order.
    *
    * 13. Ship order.
    *
    * Expected result:
    *
    * New customer successfully created. Order is created for the new customer, invoiced and shipped.
    *
    *
    */
    public function testOrderCompleteSpecialCharacters()
    {
        $email = array('email' =>  $this->generate('email', 32, 'valid'));
        $orderId = $this->OrderHelper()->createOrderForNewCustomer(false, 'products',
                $this->OrderHelper()->customerAddressGenerator(':punct:', $addrType = 'billing', $symNum = 32, TRUE),
                $this->OrderHelper()->customerAddressGenerator(':punct:', $addrType = 'shipping', $symNum = 32, TRUE),
                $email, 'Default Store View', true, true,'visa','Fixed');
        $this->clickButton('invoice', TRUE);
        $this->orderHelper()->defineId('create_invoice');
        $this->clickButton('submit_invoice', TRUE);
        $this->orderHelper()->defineId('view_order');
        $this->clickButton('ship', TRUE);
        $this->orderHelper()->defineId('create_shipment');
        $this->clickButton('submit_shipment', TRUE);
        $this->orderHelper()->defineId('view_order');
        $this->OrderHelper()->coverUpTraces(null, $email);
    }
   /**
    * Create customer via 'Create order' form (all fields are filled).
    * Create order(all fields are filled).
    *
    * Steps:
    *
    * 1.Go to Sales-Orders.
    *
    * 2.Press "Create New Order" button.
    *
    * 3.Press "Create New Customer" button.
    *
    * 4.Choose 'Main Store' (First from the list of radiobuttons) if exists.
    *
    * 5.Fill all required fields.
    *
    * 6.Press 'Add Products' button.
    *
    * 7.Add first two products.
    *
    * 8.Choose shipping address the same as billing.
    *
    * 9.Check payment method 'Check / Money order'
    *
    * 10.Choose first from 'Get shipping methods and rates'.
    *
    * 11. Submit order.
    *
    * Expected result:
    *
    * New customer successfully created. Order is created for the new customer
    *
    * Message "The order has been created." is displayed.
    *
    */
    public function testOrderCompleteAllFields()
    {
        $email = array('email' =>  $this->generate('email', 32, 'valid'));
        $orderId = $this->OrderHelper()->createOrderForNewCustomer(false, 'products',
                $this->OrderHelper()->customerAddressGenerator(':alpha:', $addrType = 'billing', $symNum = 32, FALSE),
                $this->OrderHelper()->customerAddressGenerator(':alpha:', $addrType = 'shipping', $symNum = 32, FALSE),
                $email, 'Default Store View', true, true,'visa','Fixed');
        $this->clickButton('invoice', TRUE);
        $this->orderHelper()->defineId('create_invoice');
        $this->clickButton('submit_invoice', TRUE);
        $this->orderHelper()->defineId('view_order');
        $this->clickButton('ship', TRUE);
        $this->orderHelper()->defineId('create_shipment');
        $this->clickButton('submit_shipment', TRUE);
        $this->orderHelper()->defineId('view_order');
        $this->OrderHelper()->coverUpTraces(null, $email);
    }
   /**
    * Create customer via 'Create order' form (required fields are filled).
    * Create order(required fields are filled).
    *
    * Steps:
    *
    * 1.Go to Sales-Orders.
    *
    * 2.Press "Create New Order" button.
    *
    * 3.Press "Create New Customer" button.
    *
    * 4.Choose 'Main Store' (First from the list of radiobuttons).
    *
    * 5.Fill all required fields.
    *
    * 6.Press 'Add Products' button.
    *
    * 7.Add first two products (select third options for second product).
    *
    * 8.Choose shipping address the same as billing.
    *
    * 9.Check payment method 'Check / Money order'
    *
    * 10.Choose first from 'Get shipping methods and rates'.
    *
    * 11. Submit order.
    *
    * Expected result:
    *
    * New customer successfully created. Order is created for the new customer
    *
    * Message "The order has been created." is displayed.
    *
    */
    public function testOrderCompleteReqFields()
    {
        $email = array('email' =>  $this->generate('email', 32, 'valid'));
        $orderId = $this->OrderHelper()->createOrderForNewCustomer(false, 'products',
                $this->OrderHelper()->customerAddressGenerator(':alpha:', $addrType = 'billing', $symNum = 32, TRUE),
                $this->OrderHelper()->customerAddressGenerator(':alpha:', $addrType = 'shipping', $symNum = 32, TRUE),
                $email, 'Default Store View', true, true,'visa','Fixed');
        $this->clickButton('invoice', TRUE);
        $this->orderHelper()->defineId('create_invoice');
        $this->clickButton('submit_invoice', TRUE);
        $this->orderHelper()->defineId('view_order');
        $this->clickButton('ship', TRUE);
        $this->orderHelper()->defineId('create_shipment');
        $this->clickButton('submit_shipment', TRUE);
        $this->orderHelper()->defineId('view_order');
        $this->OrderHelper()->coverUpTraces(null, $email);
    }
}
