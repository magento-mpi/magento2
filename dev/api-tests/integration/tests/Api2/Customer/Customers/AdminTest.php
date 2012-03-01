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
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Magento
 * @package     Magento_Test
 * @subpackage  integration_tests
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magento.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Test for customers API2 by admin api user
 *
 * @category    Magento
 * @package     Magento_Test
 * @author      Magento Api Team <api-team@magento.com>
 */
class Api2_Customer_Customers_AdminTest extends Magento_Test_Webservice_Rest_Admin
{
    /**
     * Customer model instance
     *
     * @var Mage_Customer_Model_Customer
     */
    protected $_customer;

    /**
     * Customer attributes
     *
     * @var array
     */
    protected $_attributes;

    /**
     * Required customer attributes
     * @var array
     */
    protected $_requiredAttributes;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    public function setUp()
    {
        parent::setUp();

        $this->_initCustomer();
    }

    /**
     * Init customer model instance
     *
     * @return Api2_Customer_Customers_AdminTest
     */
    protected function _initCustomer()
    {
        if (null === $this->_customer) {
            $this->_customer = require dirname(__FILE__) . '/../../../../fixtures/Customer/Customer.php';
            $this->_customer->addData(array(
                'password'   => '123123q',
                'website_id' => 1,
                'group_id'   => 1
            ));
        }
        return $this;
    }

    /**
     * Get customer attributes and filter required attributes in it
     * Set attributes to class properties
     *
     * @return Api2_Customer_Customers_AdminTest
     */
    protected function _initAttributes()
    {
        if (null === $this->_customer) {
            throw new Exception('A customer was not instantiated.');
        }
        if (null === $this->_requiredAttributes) {
            $this->_attributes = $this->_customer->getAttributes();
            foreach ($this->_attributes as $attribute) {
                $label = $attribute->getFrontendLabel();
                if ($attribute->getIsRequired() && $attribute->getIsVisible()) {
                    $this->_requiredAttributes[$attribute->getAttributeCode()] = $label;
                }
            }
        }
        return $this;
    }

    /**
     * Test create customer
     */
    public function testCreate()
    {
        $response = $this->callPost('customers', $this->_customer->getData());
        list($customerId) = array_reverse(explode('/', $response->getHeader('Location')));

        /** @var $customer Mage_Customer_Model_Customer */
        $customer = Mage::getModel('customer/customer')->load($customerId);
        $this->assertGreaterThan(0, $customer->getId());

        $this->addModelToDelete($customer, true);
    }

    /**
     * Test create customer with empty required fields
     *
     * @param string $attributeCode
     * @dataProvider providerRequiredAttributes
     */
    public function testCreateEmptyRequiredField($attributeCode)
    {
        $this->_customer->setData($attributeCode, '');

        $response = $this->callPost('customers', $this->_customer->getData());
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_BAD_REQUEST, $response->getStatus());
        $responseData = $response->getBody();

        $this->assertArrayHasKey('messages', $responseData, "The response doesn't has messages.");
        $this->assertArrayHasKey('error', $responseData['messages'], "The response doesn't has errors.");

        foreach ($responseData['messages']['error'] as $error) {
            $this->assertEquals(Mage_Api2_Model_Server::HTTP_BAD_REQUEST, $error['code']);
        }
    }

    /**
     * Test create customer withous required fields
     *
     * @param string $attributeCode
     * @dataProvider providerRequiredAttributes
     */
    public function testCreateWithoutRequiredField($attributeCode)
    {
        $this->_customer->unsetData($attributeCode);

        $response = $this->callPost('customers', $this->_customer->getData());
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_BAD_REQUEST, $response->getStatus());
        $responseData = $response->getBody();

        $this->assertArrayHasKey('messages', $responseData, "The response doesn't has messages.");
        $this->assertArrayHasKey('error', $responseData['messages'], "The response doesn't has errors.");

        foreach ($responseData['messages']['error'] as $error) {
            $this->assertEquals(Mage_Api2_Model_Server::HTTP_BAD_REQUEST, $error['code']);
        }
    }

    /**
     * Data provider for testCreateEmptyRequiredField and testCreateWithoutRequiredField
     *
     * @return array
     */
    public function providerRequiredAttributes()
    {
        $this->_initCustomer()
            ->_initAttributes();

        $fields = array_keys($this->_requiredAttributes);
        $output = array();

        foreach ($fields as $field) {
            $output[] = array($field);
        }

        return $output;
    }

    /**
     * Test update customer
     */
    public function testUpdate()
    {
        $response = $this->callPut('customers', array());
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_METHOD_NOT_ALLOWED, $response->getStatus());
    }

    /**
     * Test delete customer
     */
    public function testDelete()
    {
        $response = $this->callDelete('customers');
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_METHOD_NOT_ALLOWED, $response->getStatus());
    }
}
