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

        /** @var \Magento\Customer\Service\V1\Data\CustomerBuilder $customerBuilder */
        $customerBuilder = $objectManager->get('Magento\Customer\Service\V1\Data\CustomerBuilder');
        $customerBuilder->setFirstname('Firstname')
            ->setLastname('Lastname')
            ->setEmail('customer_two@example.com');
        /** @var \Magento\Customer\Service\V1\Data\CustomerDetailsBuilder $customerDetailsBuilder */
        $customerDetailsBuilder = $objectManager->get('Magento\Customer\Service\V1\Data\CustomerDetailsBuilder');
        $customerDetailsBuilder->setCustomer($customerBuilder->create());
        $createdCustomer = $this->accountService->createAccount($customerDetailsBuilder->create());

        $subscriber->loadByEmail('customer_two@example.com');
        $this->assertTrue($subscriber->isSubscribed());
        $this->assertEquals($createdCustomer->getId(), (int)$subscriber->getCustomerId());
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

        /** @var \Magento\Newsletter\Model\Subscriber $subscriber */
        $subscriber = $objectManager->create('Magento\Newsletter\Model\Subscriber');
        $subscriber->loadByEmail('customer@example.com');
        $this->assertEquals(0, (int)$subscriber->getId());
    }
}
