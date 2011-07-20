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
class OrderForNewCustomerCheckRequiredFields_Test extends Mage_Selenium_TestCase
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
    * Creating products for testing.
    *
    * Navigate to Sales-Orders page.
    *
    */
    protected function assertPreConditions()
    {}
   /**
    * Create customer via 'Create order' form (required fields are not filled).
    *
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
    * 5.Fill all fields except one required.
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
    * Expected result:
    *
    * New customer is not created. Order is not created for the new customer. Message with "Empty required field" appears.
    *
    * @dataProvider data_emptyFields
    *
    * @param array $emptyField
    *
    */
    public function testOrderWithoutRequiredFieldsFilled($emptyField)
    {
        $this->OrderHelper()->createOrderForNewCustomer(true, 'products',
                $this->loadData(
                        'new_customer_order_billing_address_reqfields',
                        $emptyField), null, null, 'Default Store View',
                true, true,'visa','Fixed');
        $page = $this->getUimapPage('admin', 'create_order_for_new_customer');
        $fieldSet = $page->findFieldset('order_billing_address');
        foreach ($emptyField as $key => $value) {
            if ($value == '%noValue%' || !$fieldSet) {
                continue;
            }
            if ($fieldSet->findField($key) != Null) {
                $fieldXpath = $fieldSet->findField($key);
            } else {
                $fieldXpath = $fieldSet->findDropdown($key);
            }
            if (preg_match('/street_address/', $key)) {
                $fieldXpath .= "/ancestor::div[@class='multi-input']";
            }
            $this->addParameter('fieldXpath', $fieldXpath);
        }   $this->addParameter('fieldXpath', $fieldXpath);
        //Check if message appears.
        $this->assertTrue($this->errorMessage('empty_required_field'), $this->messages);
        $this->assertTrue($this->verifyMessagesCount(), $this->messages);
    }
    public function data_emptyFields()
    {
        return array(
            array(array(    'billing_first_name'     => ''
                            )),
            array(array(
                            'billing_last_name'      => ''
                            )),
            array(array(
                            'billing_street_address_1'   => ''
                            )),
            array(array(
                            'billing_city'    =>  ''
                            )),
            array(array(
                            'billing_zip_code' =>  ''
                            )),
            array(array(
                            'billing_telephone'   =>  ''
                            ))
        );
    }
}
