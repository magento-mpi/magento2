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
class Order_Create_WithAddressTest extends Mage_Selenium_TestCase
{
    /**
     * Preconditions:
     *
     * Log in to Backend.
     * Create products for testing.
     */
    public function setUpBeforeTests()
    {
        $this->loginAdminUser();
        $this->OrderHelper()->createProducts('product_to_order1', TRUE);
        $this->OrderHelper()->createProducts('product_to_order2', TRUE);
    }
    protected function assertPreConditions()
    {}

    /**
     * @TODO
     * @test
     */
    public function existsEqual()
    {
        $userData = $this->loadData('new_customer', NULL, 'email');
        $addressData = $this->loadData('new_customer_address');
        $this->navigate('manage_customers');
        $this->assertTrue($this->checkCurrentPage('manage_customers'), 'Wrong page is opened');
        $searchData = array ('email' => $userData['email']);
        if ($this->OrderHelper()->search($searchData) == false){
            $this->CustomerHelper()->createCustomer($userData, $addressData);
            $this->assertTrue($this->successMessage('success_saved_customer'), $this->messages);
            $this->assertTrue($this->checkCurrentPage('manage_customers'),
                'After successful customer creation should be redirected to Manage Customers page');
        }
        $email = array('email'=> $userData['email']);
        $data = array_merge($userData, $addressData);
        $dataBilling = array(
                'billing_prefix'           => $data['prefix'],
                'billing_first_name'       => $data['first_name'],
                'billing_middle_name'      => $data['middle_name'],
                'billing_last_name'        => $data['last_name'],
                'billing_suffix'           => $data['suffix'],
                'billing_company'          => $data['company'],
                'billing_street_address_1' => $data['street_address_line_1'],
                'billing_street_address_2' => $data['street_address_line_1'],
                'billing_region'           => $data['region'],
                'billing_city'             => $data['city'],
                'billing_zip_code'         => $data['zip_code'],
                'billing_telephone'        => $data['telephone'],
                'billing_fax'              => $data['fax']);
        $dataShipping = array(
                'shipping_prefix'           => $data['prefix'],
                'shipping_first_name'       => $data['first_name'],
                'shipping_middle_name'      => $data['middle_name'],
                'shipping_last_name'        => $data['last_name'],
                'shipping_suffix'           => $data['suffix'],
                'shipping_company'          => $data['company'],
                'shipping_street_address_1' => $data['street_address_line_1'],
                'shipping_street_address_2' => $data['street_address_line_1'],
                'shipping_region'           => $data['region'],
                'shipping_city'             => $data['city'],
                'shipping_zip_code'         => $data['zip_code'],
                'shipping_telephone'        => $data['telephone'],
                'shipping_fax'              => $data['fax']);
        $orderId = $this->OrderHelper()->createOrderForExistingCustomer(false, 'Default Store View', 'products',
                $userData['email'], $dataBilling, $dataShipping, 'visa','Fixed');
        $this->OrderHelper()->coverUpTraces($orderId, $email);
    }

    /**
     * @TODO
     */
    public function test_Exists_Differ()
    {
        // @TODO
        $this->markTestIncomplete();
    }

    /**
     * @TODO
     */
    public function test_New_Equal_WithOutSave()
    {
        // @TODO
        $this->markTestIncomplete();
    }

    /**
     * @TODO
     */
    public function test_New_Differ_WithOutSave()
    {
        // @TODO
        $this->markTestIncomplete();
    }

    /**
     * @TODO
     */
    public function test_New_Equal_WithSave()
    {
        // @TODO
        $this->markTestIncomplete();
    }

    /**
     * @TODO
     */
    public function test_New_Differ_WithSave()
    {
        // @TODO
        $this->markTestIncomplete();
    }
}
