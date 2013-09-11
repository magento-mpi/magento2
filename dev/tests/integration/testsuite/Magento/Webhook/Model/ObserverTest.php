<?php
/**
 * \Magento\Webhook\Model\Observer
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Webhook
 *
 * @copyright   {copyright}
 * @license     {license_link}
 *
 * @magentoDbIsolation enabled
 */
class Magento_Webhook_Model_ObserverTest extends PHPUnit_Framework_TestCase
{
    /** @var \Magento\Webhook\Model\Subscription */
    private $_subscription;

    /** @var \Magento\Webapi\Model\Acl\User */
    private $_user;

    /** @var \Magento\Webapi\Model\Acl\Role */
    private $_role;

    /** @var \Magento\Webhook\Model\Endpoint */
    private $_endpoint;

    /** @var \Magento\Webhook\Model\Subscription\Factory */
    private $_subscriptionFactory;

    public function setUp()
    {
        $objectManager = Magento_TestFramework_Helper_Bootstrap::getObjectManager();

        /** @var $factory \Magento\Webhook\Model\Subscription\Factory */
        $this->_subscriptionFactory = $objectManager->create('Magento\Webhook\Model\Subscription\Factory');

        $this->_subscription = $objectManager->create('Magento\Webhook\Model\Subscription\Factory')
            ->create()
            ->setName('dummy')
            ->setEndpointUrl('http://localhost')
            ->save();

        $this->_role = $objectManager->create('Magento\Webapi\Model\Acl\Role')
            ->setData(array( 'role_name' => 'Test role'))
            ->save();

        $allowResourceId = 'test/get';
        /** @var \Magento\Webapi\Model\Acl\Rule $rule */
        $rule = $objectManager->create('Magento\Webapi\Model\Acl\Rule');
        $rule->setData(array(
            'resource_id' => $allowResourceId,
            'role_id' => $this->_role->getId()
        ));
        $rule->save();

        $this->_user = Mage::getModel('Magento\Webapi\Model\Acl\User')
            ->setData(array(
            'api_key' => 'webhook_test_username',
            'secret' => 'webhook_test_secret',
            'contact_email' => 'null@null.com',
            'role_id' => $this->_role->getId()
        ))->save();

        $this->_endpoint = $objectManager->create('Magento\Webhook\Model\Endpoint')
            ->load($this->_subscription->getEndpointId());
    }

    public function testAfterWebapiUserDelete()
    {
        //setup
        $this->_subscription->setStatus(\Magento\PubSub\SubscriptionInterface::STATUS_ACTIVE)
            ->save();

        //action
        $this->_user->delete();

        //verify
        $this->assertEquals((\Magento\PubSub\SubscriptionInterface::STATUS_INACTIVE),
            $this->_subscriptionFactory->create()->load($this->_subscription->getId())->getStatus());
    }

    /**
     * @magentoConfigFixture global/webhook/webhooks/test/hook/label 'Test Hook'
     */
    public function testAfterWebapiUserChange()
    {
        //setup
        $this->_subscription->setStatus(\Magento\PubSub\SubscriptionInterface::STATUS_ACTIVE)
            ->setTopics(array('test/hook'))
            ->save();
        $this->_endpoint->setApiUserId($this->_user->getUserId())
            ->save();

        //action
        $this->_user->setSecret('new secret')->save();

        //verify
        $this->assertEquals((\Magento\PubSub\SubscriptionInterface::STATUS_INACTIVE),
            $this->_subscriptionFactory->create()->load($this->_subscription->getId())->getStatus());
    }

    /**
     * @magentoConfigFixture global/webhook/webhooks/test/hook/label 'Test Hook'
     */
    public function testAfterWebapiRoleChange()
    {
        //setup
        $this->_subscription->setStatus(\Magento\PubSub\SubscriptionInterface::STATUS_ACTIVE)
            ->setTopics(array('test/hook'))
            ->save();
        $this->_endpoint->setApiUserId($this->_user->getUserId())
            ->save();

        //action
        $this->_role->setRoleName('a new name')->save();

        //verify
        $this->assertEquals((\Magento\PubSub\SubscriptionInterface::STATUS_INACTIVE),
            $this->_subscriptionFactory->create()->load($this->_subscription->getId())->getStatus());
    }

    public function tearDown()
    {
        $this->_subscription->delete();
        $this->_user->delete();
        $this->_role->delete();
    }
}
