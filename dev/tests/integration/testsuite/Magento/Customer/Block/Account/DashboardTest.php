<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Block\Account;

use Magento\TestFramework\Helper\Bootstrap;

class DashboardTest extends \PHPUnit_Framework_TestCase
{
    /** @var Dashboard */
    private $block;

    /** @var \Magento\Customer\Model\Session */
    private $customerSession;

    /** @var \Magento\Customer\Service\V1\CustomerServiceInterface */
    private $customerService;

    /**
     * Execute per test initialization.
     */
    public function setUp()
    {
        $this->customerSession =
            Bootstrap::getObjectManager()->get('Magento\Customer\Model\Session');
        $this->customerService =
            Bootstrap::getObjectManager()
                ->get('Magento\Customer\Service\V1\CustomerServiceInterface');

        $this->block = Bootstrap::getObjectManager()->get('Magento\View\LayoutInterface')
            ->createBlock(
                'Magento\Customer\Block\Account\Dashboard',
                '',
                [
                    'customerSession' => $this->customerSession,
                    'customerService' => $this->customerService
                ]
            );
    }

    /**
     * Execute per test cleanup.
     */
    public function tearDown()
    {
        $this->customerSession->unsCustomerId();
    }

    /**
     * Verify that the Dashboard::getCustomer() method returns a valid Customer Dto.
     *
     * @magentoDataFixture Magento/Customer/_files/customer.php
     */
    public function testGetCustomer()
    {
        $customer = $this->customerService->getCustomer(1);
        $this->customerSession->setCustomerId(1);
        $object = $this->block->getCustomer();
        $this->assertEquals($customer, $object);
        $this->assertInstanceOf('Magento\Customer\Service\V1\Data\Customer', $object);
    }

    /**
     * Verify that the specified customer has neither a default billing no shipping address.
     *
     * @magentoDataFixture Magento/Customer/_files/customer_no_address.php
     */
    public function testGetPrimaryAddressesNoAddresses()
    {
        $this->customerSession->setCustomerId(5);
        $this->assertFalse($this->block->getPrimaryAddresses());
    }

    /**
     * Verify that the specified customer has the same default billing and shipping address.
     *
     * @magentoDataFixture Magento/Customer/_files/customer.php
     * @magentoDataFixture Magento/Customer/_files/customer_address.php
     */
    public function testGetPrimaryAddressesBillingShippingSame()
    {
        $customer = $this->customerService->getCustomer(1);
        $this->customerSession->setCustomerId(1);
        $addresses = $this->block->getPrimaryAddresses();
        $this->assertCount(1, $addresses);
        $address = $addresses[0];
        $this->assertInstanceOf('Magento\Customer\Service\V1\Data\Address', $address);
        $this->assertEquals($customer->getDefaultBilling(), $address->getId());
        $this->assertEquals($customer->getDefaultShipping(), $address->getId());
    }

    /**
     * Verify that the specified customer has different default billing and shipping addresses.
     *
     * @magentoDataFixture Magento/Customer/_files/customer.php
     * @magentoDataFixture Magento/Customer/_files/customer_primary_addresses.php
     */
    public function testGetPrimaryAddressesBillingShippingDifferent()
    {
        $this->customerSession->setCustomerId(1);
        $addresses = $this->block->getPrimaryAddresses();
        $this->assertCount(2, $addresses);
        $this->assertNotEquals($addresses[0], $addresses[1]);
        $this->assertTrue($addresses[0]->isDefaultBilling());
        $this->assertTrue($addresses[1]->isDefaultShipping());
    }
}
