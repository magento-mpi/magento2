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
 * Test for customers API2 by customer api user
 *
 * @category    Magento
 * @package     Magento_Test
 * @author      Magento Api Team <api-team@magento.com>
 */
class Api2_Customer_Customers_CustomerTest extends Magento_Test_Webservice_Rest_Customer
{
    /**
     * Customer count of collection
     */
    const CUSTOMER_COLLECTION_COUNT = 5;

    /**
     * Generate customers to test collection
     */
    protected function _generateCustomers()
    {
        $counter = 0;
        while ($counter++ < self::CUSTOMER_COLLECTION_COUNT) {
            /** @var $customer Mage_Customer_Model_Customer */
            $customer = Mage::getModel('customer/customer');
            $customer->setData($this->_customer)
                ->setEmail(mt_rand() . 'customer.example.com')
                ->save();

            $this->addModelToDelete($customer, true);
        }
    }

    /**
     * Test create customer
     */
    public function testCreate()
    {
        $response = $this->callPost('customers', array('qwerty'));
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_FORBIDDEN, $response->getStatus());
    }

    /**
     * Test retrieve customer collection
     */
    public function testRetrieve()
    {
        $this->_generateCustomers();

        $response = $this->callGet('customers');
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_OK, $response->getStatus());

        /** @var $customer Mage_Customer_Model_Customer */
        $customer = $this->getDefaultCustomer();

        $data = $response->getBody();
        $this->assertCount(1, $data);

        foreach (array_shift($data) as $key => $value) {
            // hasData is needed for the run of the remote build on kpas (customer doesn't have the email key)
            if ($customer->hasData($key)) {
                $this->assertEquals($customer->getData($key), $value);
            }
        }
    }

    /**
     * Test update action
     */
    public function testUpdate()
    {
        $response = $this->callPut('customers', array('qwerty'));
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_FORBIDDEN, $response->getStatus());
    }

    /**
     * Test delete action
     */
    public function testDelete()
    {
        $response = $this->callDelete('customers', array('qwerty'));
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_FORBIDDEN, $response->getStatus());
    }
}
