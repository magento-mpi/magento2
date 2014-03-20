<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Newsletter\Model;

/**
 * @magentoAppIsolation enabled
 */
class ObserverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @magentoAppArea adminhtml
     * @magentoDataFixture Magento/Newsletter/_files/subscribers.php
     */
    public function testCustomerDeletedAdminArea()
    {
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();

        /** @var \Magento\Newsletter\Model\Subscriber $subscriber */
        $subscriber = $objectManager->create('Magento\Newsletter\Model\Subscriber');
        $subscriber->loadByEmail('customer@example.com');
        $this->assertTrue($subscriber->isSubscribed());

        /** @var \Magento\Customer\Model\Customer $customer */
        $customer = $objectManager->create('Magento\Customer\Model\Customer')->load(1);
        $customer->delete();

        /** @var \Magento\Newsletter\Model\Subscriber $subscriber */
        $subscriber = $objectManager->create('Magento\Newsletter\Model\Subscriber');
        $subscriber->loadByEmail('customer@example.com');
        $this->assertEquals(0, (int)$subscriber->getId());
    }

    /**
     * @magentoAppArea frontend
     * @magentoDataFixture Magento/Newsletter/_files/subscribers.php
     */
    public function testCustomerDeletedFrontendArea()
    {
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();

        /** @var \Magento\Newsletter\Model\Subscriber $subscriber */
        $subscriber = $objectManager->create('Magento\Newsletter\Model\Subscriber');
        $subscriber->loadByEmail('customer@example.com');
        $this->assertTrue($subscriber->isSubscribed());

        /** @var \Magento\Customer\Model\Customer $customer */
        $customer = $objectManager->create('Magento\Customer\Model\Customer')->load(1);
        $customer->delete();

        /** @var \Magento\Newsletter\Model\Subscriber $subscriber */
        $subscriber = $objectManager->create('Magento\Newsletter\Model\Subscriber');
        $subscriber->loadByEmail('customer@example.com');
        $this->assertEquals(0, (int)$subscriber->getId());
    }

    /**
     * @magentoAppArea adminhtml
     * @magentoDataFixture Magento/Newsletter/_files/subscribers.php
     */
    public function testCustomerDeletedNoServiceDataObject()
    {
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();

        /** @var \Magento\Newsletter\Model\Subscriber $subscriber */
        $subscriber = $objectManager->create('Magento\Newsletter\Model\Subscriber');
        $subscriber->loadByEmail('customer@example.com');
        $this->assertTrue($subscriber->isSubscribed());

        /** @var \Magento\Customer\Model\Customer $customer */
        $customer = $objectManager->create('Magento\Customer\Model\Customer')->load(1);

        $data = array(
            'data_object'       => $customer,
            'customer' => $customer,
            'customer_service_data_object' => null,
        );

        /** @var $eventManager \Magento\Event\ManagerInterface */
        $eventManager = $objectManager->get('Magento\Event\ManagerInterface');
        $eventManager->dispatch('customer_delete_after', $data);

        /** @var \Magento\Newsletter\Model\Subscriber $subscriber */
        $subscriber = $objectManager->create('Magento\Newsletter\Model\Subscriber');
        $subscriber->loadByEmail('customer@example.com');
        $this->assertNotEmpty((int)$subscriber->getId());
    }

    /**
     * @magentoDataFixture Magento/Newsletter/_files/subscribers.php
     */
    public function testCustomerDeletedOtherArea()
    {
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();

        /** @var \Magento\Newsletter\Model\Subscriber $subscriber */
        $subscriber = $objectManager->create('Magento\Newsletter\Model\Subscriber');
        $subscriber->loadByEmail('customer@example.com');
        $this->assertTrue($subscriber->isSubscribed());

        /** @var \Magento\Customer\Model\Customer $customer */
        $customer = $objectManager->create('Magento\Customer\Model\Customer')->load(1);
        $customer->delete();

        /** @var \Magento\Newsletter\Model\Subscriber $subscriber */
        $subscriber = $objectManager->create('Magento\Newsletter\Model\Subscriber');
        $subscriber->loadByEmail('customer@example.com');
        $this->assertNotEmpty((int)$subscriber->getId());
    }
}
