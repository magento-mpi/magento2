<?php
/**
 * \Magento\Webhook\Model\Resource\Endpoint
 *
 * @magentoDbIsolation enabled
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Webhook
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webhook\Model\Resource;

class EndpointTest extends \PHPUnit_Framework_TestCase
{
    /** @var  \Magento\Webhook\Model\Resource\Endpoint */
    private $_endpointResource;

    /**
     * @var \Magento\ObjectManager
     */
    protected $_objectManager;

    public function setUp()
    {
        $this->_objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $this->_endpointResource = $this->_objectManager->get('Magento\Webhook\Model\Resource\Endpoint');
    }

    public function testGetApiUserEndpoints()
    {
        // Set up the users to be associated with endpoints
        $apiUserId = $this->_objectManager->create('Magento\Webapi\Model\Acl\User')
            ->setDataChanged(true)
            ->setApiKey('api_key1')
            ->save()
            ->getUserId();
        $wrongApiUserId = $this->_objectManager->create('Magento\Webapi\Model\Acl\User')
            ->setDataChanged(true)
            ->setApiKey('api_key2')
            ->save()
            ->getUserId();
        $this->_objectManager->create('Magento\Webhook\Model\User', array('webapiUserId' => $apiUserId));
        $this->_objectManager->create('Magento\Webhook\Model\User', array('webapiUserId' => $wrongApiUserId));

        $endpointIds = array();

        // All of these should be returned
        for ($i = 0; $i < 3; $i++) {
            $endpointIds[] = $this->_objectManager
                ->create('Magento\Webhook\Model\Endpoint')
                ->setApiUserId($apiUserId)
                ->save()
                ->getId();
        }

        // None of these should be returned
        for ($i = 0; $i < 3; $i++) {
            $this->_objectManager->create('Magento\Webhook\Model\Endpoint')
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
        $apiUserId = $this->_objectManager->create('Magento\Webapi\Model\Acl\User')
            ->setDataChanged(true)
            ->setApiKey('api_key3')
            ->save()
            ->getUserId();
        $this->_objectManager->create('Magento\Webhook\Model\User', array('webapiUserId' => $apiUserId));

        $endpointIdsToFind = array();

        // All of these should be returned
        for ($i = 0; $i < 3; $i++) {
            $endpointIdsToFind[] = $this->_objectManager
                ->create('Magento\Webhook\Model\Endpoint')
                ->setApiUserId(null)
                ->save()
                ->getId();
        }

        // None of these should be returned
        for ($i = 0; $i < 3; $i++) {
            $this->_objectManager->create('Magento\Webhook\Model\Endpoint')
                ->setApiUserId($apiUserId)
                ->save()
                ->getId();
        }

        // Test retrieving them
        $this->assertEquals($endpointIdsToFind, $this->_endpointResource->getEndpointsWithoutApiUser());
    }
}
