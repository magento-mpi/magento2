<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Block\Adminhtml\Edit\Tab\View;

use Magento\Customer\Controller\RegistryConstants;

/**
 * @magentoAppArea adminhtml
 */
class AccordionTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\Core\Model\Layout */
    protected $layout;

    /** @var \Magento\Core\Model\Registry */
    protected $registry;

    /** @var \Magento\Customer\Service\V1\CustomerServiceInterface */
    protected $customerService;

    /** @var \Magento\Backend\Model\Session */
    protected $backendSession;

    protected function setUp()
    {
        parent::setUp();
        /** @var $objectManager \Magento\TestFramework\ObjectManager */
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $this->registry = $objectManager->get('Magento\Registry');
        $this->customerService = $objectManager->get('Magento\Customer\Service\V1\CustomerServiceInterface');
        $this->backendSession = $objectManager->get('Magento\Backend\Model\Session');
        $this->layout = $objectManager->create(
            'Magento\Core\Model\Layout',
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
        $customer = $this->customerService->getCustomer(1);
        $this->backendSession->setCustomerData(['account' => $customer->__toArray()]);
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
