<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Persistent\Model\Observer;

/**
 * @magentoDataFixture Magento/Persistent/_files/persistent.php
 */
class EmulateQuoteTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Customer\Service\V1\CustomerAccountServiceInterface
     */
    protected $_customerAccountService;

    /**
     * @var \Magento\Persistent\Helper\Session
     */
    protected $_persistentSessionHelper;

    /**
     * @var \Magento\Framework\ObjectManager
     */
    protected $_objectManager;

    /**
     * @var \Magento\Persistent\Model\Observer\EmulateQuote
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

        $this->_customerAccountService = $this->_objectManager->create(
            'Magento\Customer\Service\V1\CustomerAccountServiceInterface'
        );

        $this->_checkoutSession = $this->getMockBuilder(
            'Magento\Checkout\Model\Session'
        )->disableOriginalConstructor()->setMethods([])->getMock();

        $this->_persistentSessionHelper = $this->_objectManager->create('Magento\Persistent\Helper\Session');

        $this->_observer = $this->_objectManager->create(
            'Magento\Persistent\Model\Observer\EmulateQuote',
            [
                'customerAccountService' => $this->_customerAccountService,
                'checkoutSession' => $this->_checkoutSession,
                'persistentSession' => $this->_persistentSessionHelper
            ]
        );
    }

    /**
     * @magentoConfigFixture current_store persistent/options/enabled 1
     * @magentoConfigFixture current_store persistent/options/remember_enabled 1
     * @magentoConfigFixture current_store persistent/options/remember_default 1
     * @magentoAppArea frontend
     * @magentoConfigFixture current_store persistent/options/shopping_cart 1
     * @magentoConfigFixture current_store persistent/options/logout_clear 0
     */
    public function testEmulateQuote()
    {
        $requestMock = $this->getMockBuilder(
            'Magento\Framework\App\Request\Http'
        )->disableOriginalConstructor()->setMethods(
            []
        )->getMock();
        $requestMock->expects($this->once())->method('getFullActionName')->will($this->returnValue('valid_action'));
        $event = new \Magento\Framework\Event(['request' => $requestMock]);
        $observer = new \Magento\Framework\Event\Observer();
        $observer->setEvent($event);

        $this->_customerSession->loginById(1);

        $customer = $this->_customerAccountService->getCustomer(
            $this->_persistentSessionHelper->getSession()->getCustomerId()
        );
        $this->_checkoutSession->expects($this->once())->method('setCustomerData')->with($customer);
        $this->_customerSession->logout();

        $this->_observer->execute($observer);
    }
}
