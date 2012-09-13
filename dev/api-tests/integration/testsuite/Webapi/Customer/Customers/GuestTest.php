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
 * Test for customers Webapi by guest api user
 *
 * @category    Magento
 * @package     Magento_Test
 * @author      Magento Api Team <api-team@magento.com>
 */
class Webapi_Customer_Customers_GuestTest extends Magento_Test_Webservice_Rest_Guest
{
    /**
     * Test create customer
     *
     * @resourceOperation customer::create
     */
    public function testCreate()
    {
        $response = $this->callPost('customers', array('qwerty'));
        $this->assertEquals(Mage_Webapi_Controller_Front_Rest::HTTP_FORBIDDEN, $response->getStatus());
    }

    /**
     * Test retrieve customers collection
     *
     * @resourceOperation customer::multiget
     */
    public function testRetrieve()
    {
        $response = $this->callGet('customers');
        $this->assertEquals(Mage_Webapi_Controller_Front_Rest::HTTP_FORBIDDEN, $response->getStatus());
    }

    /**
     * Test update action
     *
     * @resourceOperation customer::update
     */
    public function testUpdate()
    {
        $response = $this->callPut('customers', array('qwerty'));
        $this->assertEquals(Mage_Webapi_Controller_Front_Rest::HTTP_FORBIDDEN, $response->getStatus());
    }

    /**
     * Test delete action
     *
     * @resourceOperation customer::delete
     */
    public function testDelete()
    {
        $response = $this->callDelete('customers', array('qwerty'));
        $this->assertEquals(Mage_Webapi_Controller_Front_Rest::HTTP_FORBIDDEN, $response->getStatus());
    }
}
