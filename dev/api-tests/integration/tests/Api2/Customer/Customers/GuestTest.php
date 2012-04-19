<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Test
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test for customers API2 by guest api user
 *
 * @category    Magento
 * @package     Magento_Test
 * @author      Magento Api Team <api-team@magento.com>
 */
class Api2_Customer_Customers_GuestTest extends Magento_Test_Webservice_Rest_Guest
{
    /**
     * Test create customer
     */
    public function testCreate()
    {
        $response = $this->callPost('customers', array('qwerty'));
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_FORBIDDEN, $response->getStatus());
    }

    /**
     * Test retrieve customers collection
     */
    public function testRetrieve()
    {
        $response = $this->callGet('customers');
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_FORBIDDEN, $response->getStatus());
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
