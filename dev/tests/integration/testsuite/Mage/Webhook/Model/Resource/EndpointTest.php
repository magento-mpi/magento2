<?php
/**
 * Mage_Webhook_Model_Resource_Endpoint
 *
 * @magentoDbIsolation enabled
 *
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Webhook
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Webhook_Model_Resource_EndpointTest extends PHPUnit_Framework_TestCase
{
    /** @var  Mage_Webhook_Model_Resource_Endpoint */
    private $_endpointResource;

    public function setUp()
    {
        $this->_endpointResource = Mage::getObjectManager()->get('Mage_Webhook_Model_Resource_Endpoint');
    }

    public function testGetApiUserEndpoints()
    {
        // Set up the users to be associated with endpoints
        $apiUserId = Mage::getObjectManager()->create('Mage_Webapi_Model_Acl_User')
            ->setDataChanged(true)
            ->setApiKey('api_key1')
            ->save()
            ->getUserId();
        $wrongApiUserId = Mage::getObjectManager()->create('Mage_Webapi_Model_Acl_User')
            ->setDataChanged(true)
            ->setApiKey('api_key2')
            ->save()
            ->getUserId();
        Mage::getObjectManager()->create('Mage_Webhook_Model_User', array('webapiUserId' => $apiUserId));
        Mage::getObjectManager()->create('Mage_Webhook_Model_User', array('webapiUserId' => $wrongApiUserId));

        $endpointIds = array();

        // All of these should be returned
        for ($i = 0; $i < 3; $i++) {
            $endpointIds[] = Mage::getObjectManager()
                ->create('Mage_Webhook_Model_Endpoint')
                ->setApiUserId($apiUserId)
                ->save()
                ->getId();
        }

        // None of these should be returned
        for ($i = 0; $i < 3; $i++) {
            Mage::getObjectManager()
                ->create('Mage_Webhook_Model_Endpoint')
                ->setApiUserId($wrongApiUserId)
                ->save()
                ->getId();
        }

        // Test retrieving them
        $this->assertEquals($endpointIds, $this->_endpointResource->getApiUserEndpoints($apiUserId));
    }

    public function testGetEndpointsWithoutApiUser()
    {
        // Set up the user to be associated with endpoints
        $apiUserId = Mage::getObjectManager()->create('Mage_Webapi_Model_Acl_User')
            ->setDataChanged(true)
            ->setApiKey('api_key3')
            ->save()
            ->getUserId();
        Mage::getObjectManager()->create('Mage_Webhook_Model_User', array('webapiUserId' => $apiUserId));

        $endpointIdsToFind = array();

        // All of these should be returned
        for ($i = 0; $i < 3; $i++) {
            $endpointIdsToFind[] = Mage::getObjectManager()
                ->create('Mage_Webhook_Model_Endpoint')
                ->setApiUserId(null)
                ->save()
                ->getId();
        }

        // None of these should be returned
        for ($i = 0; $i < 3; $i++) {
            Mage::getObjectManager()
                ->create('Mage_Webhook_Model_Endpoint')
                ->setApiUserId($apiUserId)
                ->save()
                ->getId();
        }

        // Test retrieving them
        $this->assertEquals($endpointIdsToFind, $this->_endpointResource->getEndpointsWithoutApiUser());
    }
}