<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Block\Account\Dashboard;

use Magento\Exception\NoSuchEntityException;

/**
 * Test class for \Magento\Customer\Block\Account\Dashboard\Info.
 */
class InfoTest extends \PHPUnit_Framework_TestCase
{
    /** Constant values used for testing */
    const CUSTOMER_ID = 1;
    const CHANGE_PASSWORD_URL = 'http://localhost/index.php/account/edit/changepass/1';
    const EMAIL_ADDRESS = 'john.doe@ebay.com';

    /** @var \PHPUnit_Framework_MockObject_MockObject | \Magento\View\Element\Template\Context */
    private $_context;

    /** @var \PHPUnit_Framework_MockObject_MockObject | \Magento\Customer\Model\Session */
    private $_customerSession;

    /** @var \PHPUnit_Framework_MockObject_MockObject | \Magento\Customer\Service\V1\CustomerServiceInterface */
    private $_customerService;

    /** @var \PHPUnit_Framework_MockObject_MockObject | \Magento\Customer\Service\V1\Dto\Customer */
    private $_customer;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject | \Magento\Customer\Helper\View
     */
    private $_helperView;

    /** @var \PHPUnit_Framework_MockObject_MockObject | \Magento\Newsletter\Model\Subscriber */
    private $_subscriber;

    /** @var \PHPUnit_Framework_MockObject_MockObject | \Magento\Newsletter\Model\SubscriberFactory */
    private $_subscriberFactory;

    /** @var \PHPUnit_Framework_MockObject_MockObject | \Magento\Customer\Block\Form\Register */
    private $_formRegister;

    /** @var Info */
    private $_block;

    public function setUp()
    {
        $urlBuilder = $this->getMockForAbstractClass('Magento\UrlInterface', array(), '', false);
        $urlBuilder->expects($this->any())->method('getUrl')->will($this->returnValue(self::CHANGE_PASSWORD_URL));

        $layout = $this->getMockForAbstractClass('Magento\View\LayoutInterface', array(), '', false);
        $this->_formRegister = $this->getMock('Magento\Customer\Block\Form\Register', array(), array(), '', false);
        $layout->expects($this->any())
            ->method('getBlockSingleton')
            ->with('Magento\Customer\Block\Form\Register')->will($this->returnValue($this->_formRegister));

        $this->_context = $this->getMock('Magento\View\Element\Template\Context', array(), array(), '', false);
        $this->_context->expects($this->once())->method('getUrlBuilder')->will($this->returnValue($urlBuilder));
        $this->_context->expects($this->once())->method('getLayout')->will($this->returnValue($layout));

        $this->_customerSession = $this->getMock('Magento\Customer\Model\Session', array(), array(), '', false);
        $this->_customerSession->expects($this->any())->method('getId')->will($this->returnValue(self::CUSTOMER_ID));

        $this->_customerService = $this->getMockForAbstractClass(
            'Magento\Customer\Service\V1\CustomerServiceInterface', array(), '', false
        );
        $this->_customer = $this->getMock('Magento\Customer\Service\V1\Dto\Customer', array(), array(), '', false);
        $this->_customer->expects($this->any())->method('getEmail')->will($this->returnValue(self::EMAIL_ADDRESS));
        $this->_customerService
            ->expects($this->any())->method('getCustomer')->will($this->returnValue($this->_customer));
        $this->_helperView = $this->getMockBuilder('\Magento\Customer\Helper\View')->disableOriginalConstructor()
            ->getMock();
        $this->_subscriberFactory =
            $this->getMock('Magento\Newsletter\Model\SubscriberFactory', array('create'), array(), '', false);
        $this->_subscriber = $this->getMock('Magento\Newsletter\Model\Subscriber', array(), array(), '', false);
        $this->_subscriber->expects($this->any())->method('loadByEmail')->will($this->returnSelf());
        $this->_subscriberFactory
            ->expects($this->any())->method('create')->will($this->returnValue($this->_subscriber));

        $this->_block = new Info(
            $this->_context,
            $this->_customerSession,
            $this->_customerService,
            $this->_subscriberFactory,
            $this->_helperView
        );
    }

    public function testGetCustomer()
    {
        $this->_customer->expects($this->once())->method('getCustomerId')->will($this->returnValue(self::CUSTOMER_ID));
        $customer = $this->_block->getCustomer();
        $this->assertEquals(self::CUSTOMER_ID, $customer->getCustomerId());
    }

    public function testGetCustomerException()
    {
        $this->_customerService
            ->expects($this->once())
            ->method('getCustomer')->will($this->throwException(new NoSuchEntityException('customerId', 1)));
        $this->assertNull($this->_block->getCustomer());
    }

    public function testGetName()
    {
        $expectedValue = 'John Q Doe Jr';

        /**
         * Called three times, once for each attribute (i.e. prefix, middlename, and suffix)
         */
        $this->_helperView
            ->expects($this->any())
            ->method('getCustomerName')->will($this->returnValue($expectedValue));

        $this->assertEquals($expectedValue, $this->_block->getName());
    }

    public function testGetChangePasswordUrl()
    {
        $this->assertEquals(self::CHANGE_PASSWORD_URL, $this->_block->getChangePasswordUrl());
    }

    public function testGetSubscriptionObject()
    {
        $this->assertSame($this->_subscriber, $this->_block->getSubscriptionObject());
    }

    /**
     * @param bool $isSubscribed Is the subscriber subscribed?
     * @param bool $expectedValue The expected value - Whether the subscriber is subscribed or not.
     *
     * @dataProvider getIsSubscribedProvider
     */
    public function testGetIsSubscribed($isSubscribed, $expectedValue)
    {
        $this->_subscriber->expects($this->once())->method('isSubscribed')->will($this->returnValue($isSubscribed));
        $this->assertEquals($expectedValue, $this->_block->getIsSubscribed());
    }

    /**
     * @return array
     */
    public function getIsSubscribedProvider()
    {
        return array(
            array(true, true),
            array(false, false)
        );
    }

    /**
     * @param bool $isNewsletterEnabled Determines if the newsletter is enabled
     * @param bool $expectedValue The expected value - Whether the newsletter is enabled or not
     *
     * @dataProvider isNewsletterEnabledProvider
     */
    public function testIsNewsletterEnabled($isNewsletterEnabled, $expectedValue)
    {
        $this->_formRegister
            ->expects($this->once())
            ->method('isNewsletterEnabled')->will($this->returnValue($isNewsletterEnabled));
        $this->assertEquals($expectedValue, $this->_block->isNewsletterEnabled());
    }

    public function isNewsletterEnabledProvider()
    {
        return array(
            array(true, true),
            array(false, false)
        );
    }
}
