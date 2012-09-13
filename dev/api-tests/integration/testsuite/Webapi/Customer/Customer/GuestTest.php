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
 * Test for customer Webapi by guest api user
 *
 * @category    Magento
 * @package     Magento_Test
 * @author      Magento Api Team <api-team@magento.com>
 */
class Webapi_Customer_Customer_GuestTest extends Magento_Test_Webservice_Rest_Guest
{
    /**
     * Test create customer
     *
     * @resourceOperation customer::create
     */
    public function testCreate()
    {
        $response = $this->callPost('customers/1', array('qwerty'));
        $this->assertEquals(Mage_Webapi_Controller_Front_Rest::HTTP_FORBIDDEN, $response->getStatus());
    }

    /**
     * Test retrieve existing customer data
     *
     * @resourceOperation customer::get
     */
    public function testRetrieve()
    {
        $response = $this->callGet('customers/1');
        $this->assertEquals(Mage_Webapi_Controller_Front_Rest::HTTP_FORBIDDEN, $response->getStatus());
    }

    /**
     * Test update existing customer data
     *
     * @resourceOperation customer::update
     */
    public function testUpdate()
    {
        $response = $this->callPut('customers/1', array('qwerty'));
        $this->assertEquals(Mage_Webapi_Controller_Front_Rest::HTTP_FORBIDDEN, $response->getStatus());
    }

    /**
     * Test delete existing customer data
     *
     * @resourceOperation customer::delete
     */
    public function testDelete()
    {
        $response = $this->callDelete('customers/1');
        $this->assertEquals(Mage_Webapi_Controller_Front_Rest::HTTP_FORBIDDEN, $response->getStatus());
    }
}
