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
 * Test for customer API2 by customer api user
 *
 * @category    Magento
 * @package     Magento_Test
 * @author      Magento Api Team <api-team@magento.com>
 */
class Api2_Customer_Customer_CustomerTest extends Magento_Test_Webservice_Rest_Customer
{
    /**
     * Customer model instance
     *
     * @var Mage_Customer_Model_Customer
     */
    protected $_customer;

    /**
     * Other customer model instance
     *
     * @var Mage_Customer_Model_Customer
     */
    protected $_otherCustomer;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    public function setUp()
    {
        parent::setUp();

        $this->_customer = $this->getDefaultCustomer();

        $this->_otherCustomer = require dirname(__FILE__) . '/../../../../fixtures/Customer/Customer.php';
        $this->_otherCustomer->save();

        $this->addModelToDelete($this->_otherCustomer, true);
    }

    /**
     * Test create customer
     */
    public function testCreate()
    {
        $response = $this->callPost('customers/1', array());
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_METHOD_NOT_ALLOWED, $response->getStatus());
    }

    /**
     * Test retrieve existing customer data
     */
    public function testRetrieve()
    {
        $response = $this->callGet('customers/' . $this->_customer->getId());

        $this->assertEquals(Mage_Api2_Model_Server::HTTP_OK, $response->getStatus());

        $responseData = $response->getBody();
        $this->assertNotEmpty($responseData);

        foreach ($responseData as $field => $value) {
            $this->assertEquals($this->_customer->getData($field), $value);
        }
    }

    /**
     * Test retrieve another customer
     */
    public function testRetrieveUnavailableResource()
    {
        $response = $this->callGet('customers/' . $this->_otherCustomer->getId());
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_NOT_FOUND, $response->getStatus());
    }

    /**
     * Test update customer
     */
    public function testUpdate()
    {
        $putData = array(
            'firstname' => 'Oleg',
            'lastname'  => 'Barabash',
        );
        $response = $this->callPut('customers/' . $this->_customer->getId(), $putData);

        $this->assertEquals(Mage_Api2_Model_Server::HTTP_OK, $response->getStatus());

        /** @var $model Mage_Customer_Model_Customer */
        $model = Mage::getModel('customer/customer');

        // Reload customer
        $model->load($this->_customer->getId());

        foreach ($putData as $field => $value) {
            $this->assertEquals($model->getData($field), $value);
        }

        // Restore firstname and lastname attribute values
        $model->addData(array(
            'firstname' => $this->_customer->getFirstname(),
            'lastname' => $this->_customer->getLastname(),
        ))->save();
    }

    /**
     * Test update another customer
     */
    public function testUpdateUnavailableResource()
    {
        $response = $this->callPut('customers/' . $this->_otherCustomer->getId(), array());
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_NOT_FOUND, $response->getStatus());
    }

    /**
     * Test delete existing customer data
     */
    public function testDelete()
    {
        $response = $this->callDelete('customers/1');
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_METHOD_NOT_ALLOWED, $response->getStatus());
    }
}
