<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Block\Adminhtml\Edit;

use Magento\Customer\Controller\RegistryConstants;
use Magento\Customer\Service\V1\CustomerAccountServiceInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\Customer\Service\V1\Data\Customer;

/**
 * Class TabsTest
 *
 * @magentoAppArea adminhtml
 */
class TabsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * The edit block under test.
     *
     * @var Tabs
     */
    private $block;

    /**
     * Customer service.
     *
     * @var CustomerAccountServiceInterface
     */
    private $customerAccountService;

    /**
     * Backend context.
     *
     * @var \Magento\Backend\Block\Template\Context
     */
    private $context;

    /**
     * Core Registry.
     *
     * @var \Magento\Framework\Registry
     */
    private $coreRegistry;

    /**
     * Execute per test initialization.
     */
    public function setUp()
    {
        $objectManager = Bootstrap::getObjectManager();
        $objectManager->get('Magento\Framework\App\State')->setAreaCode('adminhtml');

        $this->context = $objectManager->get('Magento\Backend\Block\Template\Context');
        $this->customerAccountService = $objectManager->get(
            'Magento\Customer\Service\V1\CustomerAccountServiceInterface'
        );

        $this->coreRegistry = $objectManager->get('Magento\Framework\Registry');
        $this->coreRegistry->register(RegistryConstants::CURRENT_CUSTOMER_ID, 1);

        $this->block = $objectManager->get(
            'Magento\Framework\View\LayoutInterface'
        )->createBlock(
            'Magento\Customer\Block\Adminhtml\Edit\Tabs',
            '',
            array('context' => $this->context, 'registry' => $this->coreRegistry)
        );
    }

    /**
     * Execute post class cleanup after all tests have executed.
     */
    public function tearDown()
    {
        $this->coreRegistry->unregister(RegistryConstants::CURRENT_CUSTOMER_ID);
        $this->context->getBackendSession()->setCustomerData(array());
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     */
    public function testToHtml()
    {
        $customer = $this->customerAccountService->getCustomer(
            $this->coreRegistry->registry(RegistryConstants::CURRENT_CUSTOMER_ID)
        );

        $customerData['customer_id'] = $customer->getId();
        $customerData['account'] = $customer->__toArray();
        $customerData['address'] = array();
        $this->context->getBackendSession()->setCustomerData($customerData);

        $html = $this->block->toHtml();

        $this->assertContains('name="cart" title="Shopping Cart"', $html);
        $this->assertContains('name="wishlist" title="Wishlist"', $html);

        $this->assertStringMatchesFormat('%a name="account[firstname]" %s value="Firstname" %a', $html);
        $this->assertStringMatchesFormat('%a name="account[lastname]" %s value="Lastname" %a', $html);
        $this->assertStringMatchesFormat('%a name="account[email]" %s value="customer@example.com" %a', $html);
    }

    /**
     * No data fixture nor is there a customer Id set in the registry.
     */
    public function testToHtmlNoCustomerId()
    {
        $this->coreRegistry->unregister(RegistryConstants::CURRENT_CUSTOMER_ID);

        $customerData['account'] = array(
            Customer::FIRSTNAME => 'John',
            Customer::LASTNAME => 'Doe',
            Customer::EMAIL => 'john.doe@gmail.com',
            Customer::GROUP_ID => 1,
            Customer::WEBSITE_ID => 1
        );
        $customerData['address'] = array();

        $this->context->getBackendSession()->setCustomerData($customerData);

        $html = $this->block->toHtml();

        $this->assertNotContains('name="cart" title="Shopping Cart"', $html);
        $this->assertNotContains('name="wishlist" title="Wishlist"', $html);

        $this->assertStringMatchesFormat('%a name="account[firstname]" %s value="John" %a', $html);
        $this->assertStringMatchesFormat('%a name="account[lastname]" %s value="Doe" %a', $html);
        $this->assertStringMatchesFormat('%a name="account[email]" %s value="john.doe@gmail.com" %a', $html);
    }
}
