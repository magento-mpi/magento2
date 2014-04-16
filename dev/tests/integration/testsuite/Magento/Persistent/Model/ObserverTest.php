<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Persistent\Model;

/**
 * @magentoDataFixture Magento/Persistent/_files/persistent.php
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ObserverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Customer\Helper\View
     */
    protected $_customerViewHelper;

    /**
     * @var \Magento\Escaper
     */
    protected $_escaper;

    /**
     * @var \Magento\Customer\Service\V1\CustomerAccountServiceInterface
     */
    protected $_customerAccountService;

    /**
     * @var \Magento\Persistent\Helper\Session
     */
    protected $_persistentSessionHelper;

    /**
     * @var \Magento\ObjectManager
     */
    protected $_objectManager;

    /**
     * @var \Magento\Persistent\Model\Observer
     */
    protected $_observer;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var \Magento\Checkout\Model\Session | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_checkoutSession;

    public function setUp()
    {
        $this->_objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();

        $this->_customerSession = $this->_objectManager->get('Magento\Customer\Model\Session');

        $this->_customerViewHelper = $this->_objectManager->create(
            'Magento\Customer\Helper\View'
        );
        $this->_escaper = $this->_objectManager->create(
            'Magento\Escaper'
        );
        $this->_customerAccountService = $this->_objectManager->create(
            'Magento\Customer\Service\V1\CustomerAccountServiceInterface'
        );

        $this->_checkoutSession = $this->getMockBuilder(
            'Magento\Checkout\Model\Session'
        )->disableOriginalConstructor()->setMethods([])->getMock();

        $this->_persistentSessionHelper = $this->_objectManager->create('Magento\Persistent\Helper\Session');

        $this->_observer = $this->_objectManager->create(
            'Magento\Persistent\Model\Observer',
            [
                'escaper' => $this->_escaper,
                'customerViewHelper' => $this->_customerViewHelper,
                'customerAccountService' => $this->_customerAccountService,
                'checkoutSession' => $this->_checkoutSession
            ]
        );
    }

    /**
     * @magentoConfigFixture current_store persistent/options/enabled 1
     * @magentoConfigFixture current_store persistent/options/remember_enabled 1
     * @magentoConfigFixture current_store persistent/options/remember_default 1
     * @magentoAppArea frontend
     * @magentoAppIsolation enabled
     */
    public function testEmulateWelcomeBlock()
    {
        $this->_customerSession->loginById(1);

        $httpContext = new \Magento\App\Http\Context();
        $httpContext->setValue(\Magento\Customer\Helper\Data::CONTEXT_AUTH, 1, 1);
        $block = $this->_objectManager->create(
            'Magento\Sales\Block\Reorder\Sidebar',
            [
                'httpContext' => $httpContext
            ]
        );
        $this->_observer->emulateWelcomeBlock($block);
        $customerName = $this->_escaper->escapeHtml(
            $this->_customerViewHelper->getCustomerName(
                $this->_customerAccountService->getCustomer(
                    $this->_persistentSessionHelper->getSession()->getCustomerId()
                )
            )
        );
        $translation = __('Welcome, %1!', $customerName);
        $this->assertStringMatchesFormat('%A' . $translation . '%A', $block->getWelcome());
        $this->_customerSession->logout();
    }

    /**
     * @magentoConfigFixture current_store persistent/options/enabled 1
     * @magentoConfigFixture current_store persistent/options/remember_enabled 1
     * @magentoConfigFixture current_store persistent/options/remember_default 1
     * @magentoAppArea frontend
     * @magentoAppIsolation enabled
     * @magentoConfigFixture current_store persistent/options/shopping_cart 1
     * @magentoConfigFixture current_store persistent/options/logout_clear 0
     */
    public function testEmulateQuote()
    {
        $requestMock = $this->getMockBuilder('Magento\App\Request\Http')->disableOriginalConstructor()->setMethods(
            []
        )->getMock();
        $requestMock->expects($this->once())->method('getFullActionName')->will($this->returnValue('valid_action'));
        $event = new\Magento\Event(
            [
                'request' => $requestMock
            ]
        );
        $observer = new \Magento\Event\Observer();
        $observer->setEvent($event);

        $this->_customerSession->loginById(1);

        $customer = $this->_customerAccountService->getCustomer(
            $this->_persistentSessionHelper->getSession()->getCustomerId()
        );
        $this->_checkoutSession->expects($this->once())->method('setCustomerData')->with($customer);
        $this->_customerSession->logout();

        $this->_observer->emulateQuote($observer);
    }

    /**
     * @magentoAppArea frontend
     * @magentoAppIsolation enabled
     * @magentoConfigFixture current_store persistent/options/shopping_cart 1
     * @magentoConfigFixture current_store persistent/options/logout_clear 0
     * @magentoConfigFixture current_store persistent/options/enabled 1
     */
    public function testEmulateCustomer()
    {
        $observer = new \Magento\Event\Observer();

        $this->_customerSession->loginById(1);
        $this->_customerSession->logout();
        $this->assertNull($this->_customerSession->getCustomerId());
        $this->assertEquals(
            \Magento\Customer\Service\V1\CustomerGroupServiceInterface::NOT_LOGGED_IN_ID,
            $this->_customerSession->getCustomerGroupId()
        );

        $this->_observer->emulateCustomer($observer);
        $customer = $this->_customerAccountService->getCustomer(
            $this->_persistentSessionHelper->getSession()->getCustomerId()
        );
        $this->assertEquals(
            $customer->getId(),
            $this->_customerSession->getCustomerId()
        );
        $this->assertEquals(
            $customer->getGroupId(),
            $this->_customerSession->getCustomerGroupId()
        );
    }
}
