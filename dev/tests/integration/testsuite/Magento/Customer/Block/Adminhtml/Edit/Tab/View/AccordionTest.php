<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Block\Adminhtml\Edit\Tab\View;

use Magento\Customer\Controller\RegistryConstants;
use Magento\Customer\Api\CustomerRepositoryInterface;

/**
 * @magentoAppArea adminhtml
 */
class AccordionTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\Framework\View\Layout */
    protected $layout;

    /** @var \Magento\Framework\Registry */
    protected $registry;

    /** @var CustomerRepositoryInterface */
    protected $customerRepositoryInterface;

    /** @var \Magento\Backend\Model\Session */
    protected $backendSession;

    protected function setUp()
    {
        parent::setUp();
        /** @var $objectManager \Magento\TestFramework\ObjectManager */
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $this->registry = $objectManager->get('Magento\Framework\Registry');
        $this->customerRepositoryInterface = $objectManager->get(
            'Magento\Customer\Api\CustomerRepositoryInterface'
        );
        $this->backendSession = $objectManager->get('Magento\Backend\Model\Session');
        $this->layout = $objectManager->create(
            'Magento\Framework\View\Layout',
            array('area' => \Magento\Backend\App\Area\FrontNameResolver::AREA_CODE)
        );
    }

    protected function tearDown()
    {
        $this->registry->unregister(RegistryConstants::CURRENT_CUSTOMER_ID);
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     * @magentoConfigFixture customer/account_share/scope 1
     */
    public function testToHtmlEmptyWebsiteShare()
    {
        $this->registry->register(RegistryConstants::CURRENT_CUSTOMER_ID, 1);
        $block = $this->layout->createBlock('Magento\Customer\Block\Adminhtml\Edit\Tab\View\Accordion');

        $html = $block->toHtml();

        $this->assertContains('Wishlist - 0 item(s)', $html);
        $this->assertContains('Shopping Cart - 0 item(s)', $html);
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     * @magentoDataFixture Magento/Core/_files/second_third_store.php
     * @magentoConfigFixture current_store customer/account_share/scope 0
     */
    public function testToHtmlEmptyGlobalShareAndSessionData()
    {
        $this->registry->register(RegistryConstants::CURRENT_CUSTOMER_ID, 1);
        $customer = $this->customerRepositoryInterface->getById(1);
        $this->backendSession->setCustomerData(array('account' => $customer->__toArray()));
        $block = $this->layout->createBlock('Magento\Customer\Block\Adminhtml\Edit\Tab\View\Accordion');

        $html = $block->toHtml();

        $this->assertContains('Wishlist - 0 item(s)', $html);
        $this->assertContains('Shopping Cart of Main Website - 0 item(s)', $html);
        $this->assertContains('Shopping Cart of Second Website - 0 item(s)', $html);
        $this->assertContains('Shopping Cart of Third Website - 0 item(s)', $html);
    }

    /**
     * @magentoConfigFixture customer/account_share/scope 1
     */
    public function testToHtmlEmptyWebsiteShareNewCustomer()
    {
        $block = $this->layout->createBlock('Magento\Customer\Block\Adminhtml\Edit\Tab\View\Accordion');

        $html = $block->toHtml();

        $this->assertContains('Wishlist - 0 item(s)', $html);
        $this->assertContains('Shopping Cart - 0 item(s)', $html);
    }
}
