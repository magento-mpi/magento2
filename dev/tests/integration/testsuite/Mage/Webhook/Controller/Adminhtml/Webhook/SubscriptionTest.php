<?php
/**
 * Mage_Webhook_Controller_Adminhtml_Webhook_Subscription
 *
 * @magentoAppArea adminhtml
 * @magentoDbIsolation enabled
 *
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Webhook
 * @copyright   {copyright}
 * @license     {license_link}
 *
 */
class Mage_Webhook_Controller_Adminhtml_Webhook_SubscriptionTest extends Mage_Backend_Utility_Controller
{
    /** @var Mage_Webhook_Model_Subscription  */
    private $_subscription;

    public function setUp()
    {
        parent::setUp();
        $this->_createDummySubscription();
    }

    public function testIndexAction()
    {
        $this->dispatch('backend/admin/webhook_subscription/index');
        $response = $this->getResponse()->getBody();

        $this->assertContains('Subscriptions', $response);
        $this->assertSelectCount('#subscriptionGrid', 1, $response);
    }

    public function testNewAction()
    {
        $this->dispatch('backend/admin/webhook_subscription/new');
        $response = $this->getResponse()->getBody();

        $this->assertEquals('edit', $this->getRequest()->getActionName());
        $this->assertContains('entry-edit form-inline', $response);
        $this->assertContains('Add Subscription', $response);
        $this->assertSelectCount('#subscription_fieldset', 1, $response);
    }

    public function testEditAction()
    {
        $subscriptionId = $this->_subscription->getId();
        $this->getRequest()->setParam('id', $subscriptionId);
        $this->dispatch('backend/admin/webhook_subscription/edit');
        $response = $this->getResponse()->getBody();
        $saveLink = 'webhook_subscription/save/id/' . $subscriptionId;
            
        $this->assertContains('entry-edit form-inline', $response);
        $this->assertContains('Edit Subscription', $response);
        $this->assertContains($saveLink, $response);
        $this->assertSelectCount('#subscription_fieldset', 1, $response);
    }

    public function testSaveActionNoData()
    {
        $this->getRequest()->setParam('id', $this->_subscription->getId());
        $this->dispatch('backend/admin/webhook_subscription/save');
        
        $this->assertSessionMessages(
            $this->equalTo(array("The subscription 'dummy' has not been saved, as no data was provided.")),
            Magento_Core_Model_Message::ERROR
        );
        $this->assertRedirect($this->stringContains('backend/admin/webhook_subscription/edit/'));
    }

    public function testSaveActionUpdateSubscription()
    {
        $subscriptionId = $this->_subscription->getId();
        $this->getRequest()->setParam('id', $subscriptionId);
        $url = 'endpoint_url' . uniqid();
        $this->getRequest()->setPost(array(
            'endpoint_url' => $url,
        ));
        $this->dispatch('backend/admin/webhook_subscription/save');
        $this->assertSessionMessages(
            $this->equalTo(array("The subscription 'dummy' has been saved.")),
            Magento_Core_Model_Message::SUCCESS
        );
        $this->assertRedirect($this->stringContains('backend/admin/webhook_subscription/index/'));
    }

    public function testSaveActionNewSubscription()
    {
        $url = 'endpoint_url' . uniqid();
        $subscriptionName = 'new subscription';
        $this->getRequest()->setPost(array(
            'name' => $subscriptionName,
            'endpoint_url' => $url,
        ));
        $this->dispatch('backend/admin/webhook_subscription/save');

        $this->assertSessionMessages(
            $this->equalTo(array("The subscription '$subscriptionName' has been saved.")),
            Magento_Core_Model_Message::SUCCESS
        );
        $this->assertRedirect($this->stringContains('backend/admin/webhook_subscription/index/'));
    }

    public function testDeleteActionNoId()
    {
        $this->dispatch('backend/admin/webhook_subscription/delete');

        $this->assertSessionMessages(
            $this->equalTo(array("Subscription with ID '' doesn't exist.")),
            Magento_Core_Model_Message::ERROR
        );
        $this->assertRedirect($this->stringContains('backend/admin/webhook_subscription/index/'));
    }

    public function testDeleteActionWithAliasSubscription()
    {
        $this->_subscription->setAlias('alias')->save();
        $this->getRequest()->setParam('id', $this->_subscription->getId());
        $this->dispatch('backend/admin/webhook_subscription/delete');

        $this->assertSessionMessages(
            $this->equalTo(array("The subscription 'dummy' can not be removed.")),
            Magento_Core_Model_Message::ERROR
        );
        $this->assertRedirect($this->stringContains('backend/admin/webhook_subscription/index/'));
    }

    public function testDeleteAction()
    {
        $this->getRequest()->setParam('id', $this->_subscription->getId());
        $this->dispatch('backend/admin/webhook_subscription/delete');

        $this->assertSessionMessages(
            $this->equalTo(array("The subscription 'dummy' has been removed.")),
            Magento_Core_Model_Message::SUCCESS
        );
        $this->assertRedirect($this->stringContains('backend/admin/webhook_subscription/index/'));
    }

    public function testRevokeAction()
    {
        $this->getRequest()->setParam('id', $this->_subscription->getId());
        $this->dispatch('backend/admin/webhook_subscription/revoke');

        $this->assertSessionMessages(
            $this->equalTo(array("The subscription 'dummy' has been revoked.")),
            Magento_Core_Model_Message::SUCCESS
        );
        $this->assertRedirect($this->stringContains('backend/admin/webhook_subscription/index/'));
    }

    public function testActivateAction()
    {
        $this->getRequest()->setParam('id', $this->_subscription->getId());
        $this->dispatch('backend/admin/webhook_subscription/activate');

        $this->assertSessionMessages(
            $this->equalTo(array("The subscription 'dummy' has been activated.")),
            Magento_Core_Model_Message::SUCCESS
        );
        $this->assertRedirect($this->stringContains('backend/admin/webhook_subscription/index/'));
    }

    /**
     * Creates a dummy subscription for use in dispatched methods under testing
     */
    private function _createDummySubscription()
    {
        /** @var $factory Mage_Webhook_Model_Subscription_Factory */
        $factory = Mage::getObjectManager()->create('Mage_Webhook_Model_Subscription_Factory');
        $this->_subscription = $factory->create()
            ->setName('dummy')
            ->save();
    }
}
