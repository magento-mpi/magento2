<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Block\Adminhtml;

use Magento\TestFramework\Helper\Bootstrap;
use Magento\Customer\Controller\Adminhtml\Index;
use Magento\Customer\Service\V1\Dto\Customer;

/**
 * Class EditTest
 *
 * @magentoAppArea adminhtml
 * @magentoDataFixture createCustomer
 */
class EditTest extends \PHPUnit_Framework_TestCase
{
    /**
     * The edit block under test.
     *
     * @var Edit
     */
    private $block;

    /**
     * Core Registry.
     *
     * @var \Magento\Core\Model\Registry
     */
    private $coreRegistry;

    /**
     * The customer Id.
     *
     * @var int
     */
    private static $customerId;

    /**
     * Create a new Customer.
     */
    public static function createCustomer()
    {
        $customerService = Bootstrap::getObjectManager()
            ->get('Magento\Customer\Service\V1\CustomerServiceInterface');
        $customer = new Customer([
            'group_id' => 1,
            'website_id' => 1,
            'firstname' => 'Firstname',
            'lastname' => 'Lastname',
            'email' => 'firstname.lastname@gmail.com'
        ]);
        self::$customerId = $customerService->saveCustomer($customer);
    }

    /**
     * Execute per test initialization.
     */
    public function setUp()
    {
        $objectManager = Bootstrap::getObjectManager();
        $objectManager->get('Magento\App\State')->setAreaCode('adminhtml');

        $this->coreRegistry = $objectManager->get('Magento\Core\Model\Registry');
        $this->coreRegistry->register(Index::REGISTRY_CURRENT_CUSTOMER_ID, self::$customerId);

        $this->block = $objectManager->get('Magento\View\LayoutInterface')
            ->createBlock(
                'Magento\Customer\Block\Adminhtml\Edit',
                '',
                ['coreRegistry' => $this->coreRegistry]
            );
    }

    /**
     * Execute post class cleanup after all tests have executed.
     */
    public function tearDown()
    {
        $this->coreRegistry->unregister(Index::REGISTRY_CURRENT_CUSTOMER_ID);
    }

    /**
     * Verify that the customer Id is the one that was set in the registry.
     */
    public function testGetCustomerId()
    {
        $this->assertEquals(self::$customerId, $this->block->getCustomerId());
    }

    /**
     * Verify that the correct order create Url is generated.
     */
    public function testGetCreateOrderUrl()
    {
        $this->assertContains(
            'sales/order_create/start/customer_id/' . self::$customerId, $this->block->getCreateOrderUrl()
        );
    }

    /**
     * Verify that the header text is correct for a new customer.
     */
    public function testGetHeaderTextNewCustomer()
    {
        $this->coreRegistry->unregister(Index::REGISTRY_CURRENT_CUSTOMER_ID);
        $this->assertEquals('New Customer', $this->block->getHeaderText());
    }

    /**
     * Verify that the header text is correct for an existing customer.
     */
    public function testGetHeaderTextExistingCustomer()
    {
        $this->assertEquals('Firstname Lastname', $this->block->getHeaderText());
    }

    /**
     * Verify that the correct customer validation Url is generated.
     */
    public function testGetValidationUrl()
    {
        $this->assertContains('customer/index/validate', $this->block->getValidationUrl());
    }

    /**
     * Verify the basic content of the block's form Html.
     */
    public function testGetFormHtml()
    {
        $html = $this->block->getFormHtml();
        $this->assertContains('<div class="entry-edit form-inline">', $html);
        $this->assertContains(
            '<div id="product_composite_configure" class="product-configure-popup" style="display:none;">', $html
        );
        $this->assertContains('id="product_composite_configure_form"', $html);
    }
}
