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
 * Test for sales order comments API2 by guest api user
 *
 * @category    Magento
 * @package     Magento_Test
 * @author      Magento Api Team <api-team@magento.com>
 */
class Api2_Sales_Order_Comments_GuestTest extends Magento_Test_Webservice_Rest_Guest
{
    /**
     * Test create customer
     */
    public function testCreate()
    {
        $response = $this->callPost('orders/1/comments', array('qwerty'));
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_FORBIDDEN, $response->getStatus());
    }

    /**
     * Test retrieve customers collection
     */
    public function testRetrieve()
    {
        $response = $this->callGet('orders/1/comments');
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_FORBIDDEN, $response->getStatus());
    }

    /**
     * Test update action
     */
    public function testUpdate()
    {
        $response = $this->callPut('orders/1/comments', array('qwerty'));
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_FORBIDDEN, $response->getStatus());
    }

    /**
     * Test delete action
     */
    public function testDelete()
    {
        $response = $this->callDelete('orders/1/comments', array('qwerty'));
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_FORBIDDEN, $response->getStatus());
    }
}
