<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Block\Adminhtml\Edit;

use Magento\Customer\Controller\RegistryConstants;
use Magento\TestFramework\Helper\Bootstrap;

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
     * @var \Magento\Customer\Service\V1\CustomerServiceInterface
     */
    private $customerService;

    /**
     * Backend context.
     *
     * @var \Magento\Backend\Block\Template\Context
     */
    private $context;

    /**
     * Core Registry.
     *
     * @var \Magento\Core\Model\Registry
     */
    private $coreRegistry;

    /**
     * Execute per test initialization.
     */
    public function setUp()
    {
        $objectManager = Bootstrap::getObjectManager();
        $objectManager->get('Magento\App\State')->setAreaCode('adminhtml');

        $this->context = $objectManager->get('Magento\Backend\Block\Template\Context');
        $this->customerService = $objectManager->get('Magento\Customer\Service\V1\CustomerServiceInterface');

        $this->coreRegistry = $objectManager->get('Magento\Core\Model\Registry');
        $this->coreRegistry->register(RegistryConstants::CURRENT_CUSTOMER_ID, 1);

        $this->block = $objectManager->get('Magento\View\LayoutInterface')
            ->createBlock(
                'Magento\Customer\Block\Adminhtml\Edit\Tabs',
                '',
                [
                    'context' => $this->context,
                    'registry' => $this->coreRegistry
                ]
            );
    }

    /**
     * Execute post class cleanup after all tests have executed.
     */
    public function tearDown()
    {
        $this->coreRegistry->unregister(RegistryConstants::CURRENT_CUSTOMER_ID);
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     */
    public function testToHtml()
    {
        $customer = $this->customerService
            ->getCustomer($this->coreRegistry->registry(RegistryConstants::CURRENT_CUSTOMER_ID));

        $customerData['customer_id'] = $customer->getCustomerId();
        $customerData['account'] = $customer->__toArray();
        $customerData['address'] = [];
        $this->context->getBackendSession()->setCustomerData($customerData);

        $html = $this->block->toHtml();
        $this->assertStringMatchesFormat('%a name="account[firstname]" %s value="Firstname" %a', $html);
        $this->assertStringMatchesFormat('%a name="account[lastname]" %s value="Lastname" %a', $html);
        $this->assertStringMatchesFormat('%a name="account[email]" %s value="customer@example.com" %a', $html);
    }
}
