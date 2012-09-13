<?php
/**
 * {license_notice}
 *
 * @category    Paas
 * @package     tests
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
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
            $customer = Mage::getModel('Mage_Customer_Model_Customer');
            $customer->setData($this->_customer)
                ->setEmail(mt_rand() . 'customer.example.com')
                ->save();

            $this->addModelToDelete($customer, true);
        }
    }

    /**
     * Test create customer
     *
     * @resourceOperation customer::create
     */
    public function testCreate()
    {
        $response = $this->callPost('customers', array('qwerty'));
        $this->assertEquals(Mage_Api2_Controller_Front_Rest::HTTP_FORBIDDEN, $response->getStatus());
    }

    /**
     * Test retrieve customer collection
     *
     * @resourceOperation customer::multiget
     */
    public function testRetrieve()
    {
        $this->_generateCustomers();

        $response = $this->callGet('customers');
        $this->assertEquals(Mage_Api2_Controller_Front_Rest::HTTP_OK, $response->getStatus());

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
     *
     * @resourceOperation customer::update
     */
    public function testUpdate()
    {
        $response = $this->callPut('customers', array('qwerty'));
        $this->assertEquals(Mage_Api2_Controller_Front_Rest::HTTP_FORBIDDEN, $response->getStatus());
    }

    /**
     * Test delete action
     *
     * @resourceOperation customer::delete
     */
    public function testDelete()
    {
        $response = $this->callDelete('customers', array('qwerty'));
        $this->assertEquals(Mage_Api2_Controller_Front_Rest::HTTP_FORBIDDEN, $response->getStatus());
    }
}
