<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\AdvancedCheckout\Controller\Adminhtml;

class IndexTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\AdvancedCheckout\Controller\Adminhtml\Stub\Child
     */
    protected $controller;

    /**
     * @var \Magento\Framework\ObjectManagerInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $objectManager;

    /**
     * @var \Magento\Framework\App\Request\Http | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $request;

    /**
     * @var \Magento\Customer\Api\Data\CustomerDataBuilder | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $customerBuilder;

    public function setUp()
    {
        $this->objectManager = $this->getMock('Magento\Framework\ObjectManager\ObjectManager', [], [], '', false);
        $this->customerBuilder = $this->getMock(
            'Magento\Customer\Api\Data\CustomerDataBuilder',
            ['populateWithArray', 'create'],
            [],
            '',
            false
        );

        $this->request = $this->getMock('Magento\Framework\App\Request\Http', ['getPost', 'getParam'], [], '', false);
        $response = $this->getMock('Magento\Framework\App\ResponseInterface');

        $context = $this->getMock('Magento\Backend\App\Action\Context', [], [], '', false);
        $context->expects($this->once())
            ->method('getRequest')
            ->willReturn($this->request);
        $context->expects($this->once())
            ->method('getResponse')
            ->willReturn($response);
        $context->expects($this->once())
            ->method('getObjectManager')
            ->willReturn($this->objectManager);
        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->controller = $objectManager->getObject(
            'Magento\AdvancedCheckout\Controller\Adminhtml\Stub\Child',
            ['context' => $context, 'customerBuilder' => $this->customerBuilder]
        );
    }

    public function testInitData()
    {
        $this->request->expects($this->any())
            ->method('getParam')
            ->willReturn(true);

        $customerModel = $this->getMock('Magento\Customer\Model\Customer', ['getWebsiteId', 'load', 'getId'], [], '', false);
        $customerModel->expects($this->once())
            ->method('load')
            ->willReturnSelf();
        $customerModel->expects($this->once())
            ->method('getId')
            ->willReturn(true);
        $customerModel->expects($this->once())
            ->method('getWebsiteId')
            ->willReturn(true);

        $store = $this->getMock('Magento\Store\Model\Store', [], [], '', false);

        $storeManager = $this->getMock('Magento\Store\Model\StoreManager', ['getWebsiteId', 'getStore'], [], '', false);
        $storeManager->expects($this->any())
            ->method('getStore')
            ->willReturn($store);

        $quote = $this->getMock('Magento\Sales\Model\Quote', [], [], '', false);
        $quote->expects($this->once())
            ->method('getId')
            ->willReturn(false);

        $cart = $this->getMock('Magento\AdvancedCheckout\Model\Cart', [], [], '', false);
        $cart->expects($this->once())
            ->method('setSession')
            ->willReturnSelf();
        $cart->expects($this->once())
            ->method('setContext')
            ->willReturnSelf();
        $cart->expects($this->once())
            ->method('setCurrentStore')
            ->willReturnSelf();
        $cart->expects($this->once())
            ->method('getQuote')
            ->willReturn($quote);

        $session = $this->getMock('Magento\Backend\Model\Session', [], [], '', false);
        $quoteRepository = $this->getMock('Magento\Sales\Model\QuoteRepository', [], [], '', false);

        $this->objectManager->expects($this->at(0))
            ->method('create')
            ->with('Magento\Customer\Model\Customer')
            ->willReturn($customerModel);
        $this->objectManager->expects($this->at(1))
            ->method('get')
            ->with('Magento\Store\Model\StoreManager')
            ->willReturn($storeManager);
        $this->objectManager->expects($this->at(2))
            ->method('get')
            ->with('Magento\AdvancedCheckout\Model\Cart')
            ->willReturn($cart);
        $this->objectManager->expects($this->at(3))
            ->method('get')
            ->with('Magento\Backend\Model\Session')
            ->willReturn($session);
        $this->objectManager->expects($this->at(4))
            ->method('get')
            ->with('Magento\Sales\Model\QuoteRepository')
            ->willReturn($quoteRepository);

        $customerData = $this->getMock('Magento\Customer\Api\Data\CustomerInterface');

        $this->customerBuilder->expects($this->once())
            ->method('populateWithArray')
            ->willReturnSelf();
        $this->customerBuilder->expects($this->once())
            ->method('create')
            ->willReturn($customerData);
        $quote->expects($this->once())
            ->method('setStore')
            ->willReturnSelf();
        $quote->expects($this->once())
            ->method('setCustomer')
            ->with($customerData);

        $this->controller->execute();
    }
}
