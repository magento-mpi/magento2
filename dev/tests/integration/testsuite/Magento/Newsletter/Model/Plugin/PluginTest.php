<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Newsletter\Model\Plugin;

use Magento\TestFramework\Helper\Bootstrap;

/**
 * @magentoAppIsolation enabled
 */
class PluginTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Customer Account Service
     *
     * @var \Magento\Customer\Service\V1\CustomerAccountServiceInterface
     */
    private $accountService;

    public function setUp()
    {
        $this->accountService = Bootstrap::getObjectManager()
            ->get('Magento\Customer\Service\V1\CustomerAccountServiceInterface');
    }

    public function tearDown()
    {
        /** @var \Magento\Customer\Model\CustomerRegistry $customerRegistry */
        $customerRegistry = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->get('Magento\Customer\Model\CustomerRegistry');
        //Cleanup customer from registry
        $customerRegistry->remove(1);
    }

    /**
     * @magentoAppArea adminhtml
     * @magentoDataFixture Magento/Newsletter/_files/subscribers.php
     */
    public function testCustomerCreated()
    {
        $objectManager = Bootstrap::getObjectManager();

        /** @var \Magento\Newsletter\Model\Subscriber $subscriber */
        $subscriber = $objectManager->create('Magento\Newsletter\Model\Subscriber');
        $subscriber->loadByEmail('customer_two@example.com');
        $this->assertTrue($subscriber->isSubscribed());
        $this->assertEquals(0, (int)$subscriber->getCustomerId());

        /** @var \Magento\Customer\Api\Data\CustomerDataBuilder $customerBuilder */
        $customerBuilder = $objectManager->get('Magento\Customer\Api\Data\CustomerDataBuilder');
        $customerBuilder->setFirstname('Firstname')
            ->setLastname('Lastname')
            ->setEmail('customer_two@example.com');
        /** @var \Magento\Customer\Service\V1\Data\CustomerDetailsBuilder $customerDetailsBuilder */
        $customerDetailsBuilder = $objectManager->get('Magento\Customer\Service\V1\Data\CustomerDetailsBuilder');
        $customerDetailsBuilder->setCustomer($customerBuilder->create());
        $createdCustomer = $this->accountService->createCustomer($customerDetailsBuilder->create());

        $subscriber->loadByEmail('customer_two@example.com');
        $this->assertTrue($subscriber->isSubscribed());
        $this->assertEquals($createdCustomer->getId(), (int)$subscriber->getCustomerId());
    }


    /**
     * @magentoAppArea adminhtml
     * @magentoDbIsolation enabled
     */
    public function testCustomerCreatedNotSubscribed()
    {
        $this->verifySubscriptionNotExist('customer@example.com');

        $objectManager = Bootstrap::getObjectManager();
        /** @var \Magento\Customer\Api\Data\CustomerDataBuilder $customerBuilder */
        $customerBuilder = $objectManager->get('Magento\Customer\Api\Data\CustomerDataBuilder');
        $customerBuilder->setFirstname('Firstname')
            ->setLastname('Lastname')
            ->setEmail('customer@example.com');
        /** @var \Magento\Customer\Service\V1\Data\CustomerDetailsBuilder $customerDetailsBuilder */
        $customerDetailsBuilder = $objectManager->get('Magento\Customer\Service\V1\Data\CustomerDetailsBuilder');
        $customerDetailsBuilder->setCustomer($customerBuilder->create());
        $this->accountService->createCustomer($customerDetailsBuilder->create());

        $this->verifySubscriptionNotExist('customer@example.com');
    }

    /**
     * @magentoAppArea adminhtml
     * @magentoDataFixture Magento/Newsletter/_files/subscribers.php
     */
    public function testCustomerUpdatedEmail()
    {
        $objectManager = Bootstrap::getObjectManager();

        /** @var \Magento\Newsletter\Model\Subscriber $subscriber */
        $subscriber = $objectManager->create('Magento\Newsletter\Model\Subscriber');
        $subscriber->loadByEmail('customer@example.com');
        $this->assertTrue($subscriber->isSubscribed());
        $this->assertEquals(1, (int)$subscriber->getCustomerId());

        $customer = $this->accountService->getCustomer(1);
        /** @var \Magento\Customer\Api\Data\CustomerDataBuilder $customerBuilder */
        $customerBuilder = $objectManager->get('Magento\Customer\Api\Data\CustomerDataBuilder');
        $customerBuilder->populate($customer)
            ->setEmail('new@example.com');
        /** @var \Magento\Customer\Service\V1\Data\CustomerDetailsBuilder $customerDetailsBuilder */
        $customerDetailsBuilder = $objectManager->get('Magento\Customer\Service\V1\Data\CustomerDetailsBuilder');
        $customerDetailsBuilder->setCustomer($customerBuilder->create());
        $this->accountService->updateCustomer(1, $customerDetailsBuilder->create());

        $subscriber->loadByEmail('new@example.com');
        $this->assertTrue($subscriber->isSubscribed());
        $this->assertEquals(1, (int)$subscriber->getCustomerId());
    }

    /**
     * @magentoAppArea adminhtml
     * @magentoDataFixture Magento/Newsletter/_files/subscribers.php
     */
    public function testCustomerDeletedAdminArea()
    {
        $objectManager = Bootstrap::getObjectManager();

        /** @var \Magento\Newsletter\Model\Subscriber $subscriber */
        $subscriber = $objectManager->create('Magento\Newsletter\Model\Subscriber');
        $subscriber->loadByEmail('customer@example.com');
        $this->assertTrue($subscriber->isSubscribed());

        $this->accountService->deleteCustomer(1);

        $this->verifySubscriptionNotExist('customer@example.com');
    }

    /**
     * Verify a subscription doesn't exist for a given email address
     *
     * @param string $email
     * @return \Magento\Newsletter\Model\Subscriber
     */
    private function verifySubscriptionNotExist($email)
    {
        $objectManager = Bootstrap::getObjectManager();

        /** @var \Magento\Newsletter\Model\Subscriber $subscriber */
        $subscriber = $objectManager->create('Magento\Newsletter\Model\Subscriber');
        $subscriber->loadByEmail($email);
        $this->assertFalse($subscriber->isSubscribed());
        $this->assertEquals(0, (int)$subscriber->getId());
        return $subscriber;
    }
}
