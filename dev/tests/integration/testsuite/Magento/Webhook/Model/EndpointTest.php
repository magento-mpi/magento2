<?php
/**
 * \Magento\Webhook\Model\Endpoint
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webhook_Model_EndpointTest extends PHPUnit_Framework_TestCase
{
    public function testGetMethods()
    {
        /** @var  \Magento\Webhook\Model\Endpoint $endpoint */
        $endpoint = Mage::getModel('\Magento\Webhook\Model\Endpoint');

        $endpoint->setEndpointUrl('endpoint.url.com');
        $this->assertEquals('endpoint.url.com', $endpoint->getEndpointUrl());

        $endpoint->setTimeoutInSecs('9001');
        $this->assertEquals('9001', $endpoint->getTimeoutInSecs());

        $endpoint->setFormat('JSON');
        $this->assertEquals('JSON', $endpoint->getFormat());

        $endpoint->setAuthenticationType('basic');
        $this->assertEquals('basic', $endpoint->getAuthenticationType());

        // test getUser
        $endpoint->setApiUserId(null);
        $this->assertEquals(null, $endpoint->getUser());

        $userId = 42;
        $user = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento\Webhook\Model\User', array('webapiUserId' => $userId));
        $endpoint->setApiUserId($userId);
        $this->assertEquals($user, $endpoint->getUser());

    }

    public function testBeforeSave()
    {
        /** @var  \Magento\Webhook\Model\Endpoint $endpoint */
        $endpoint = Mage::getModel('\Magento\Webhook\Model\Endpoint');
        $endpoint->setUpdatedAt('-1')
            ->save();

        $this->assertEquals('none', $endpoint->getAuthenticationType());
        $this->assertFalse($endpoint->getUpdatedAt() == '-1');
        $endpoint->delete();
    }
}
