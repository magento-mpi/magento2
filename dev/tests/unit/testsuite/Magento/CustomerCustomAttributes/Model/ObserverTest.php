<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CustomerCustomAttributes\Model;

use Magento\CustomerCustomAttributes\Helper\Data as Helper;
use Magento\CustomerCustomAttributes\Model\Sales\Order\AddressFactory as OrderAddressFactory;
use Magento\CustomerCustomAttributes\Model\Sales\OrderFactory;
use Magento\CustomerCustomAttributes\Model\Sales\Quote\AddressFactory as QuoteAddressFactory;
use Magento\CustomerCustomAttributes\Model\Sales\QuoteFactory;

class ObserverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Observer
     */
    protected $observer;

    /**
     * @var Helper|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $helper;

    /**
     * @var OrderFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $orderFactory;

    /**
     * @var OrderAddressFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $orderAddressFactory;

    /**
     * @var QuoteFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $quoteFactory;

    /**
     * @var QuoteAddressFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $quoteAddressFactory;

    public function setUp()
    {
        $this->helper = $helper = $this->getMockBuilder('Magento\CustomerCustomAttributes\Helper\Data')
            ->disableOriginalConstructor()
            ->getMock();
        /** @var Helper $helper */

        $this->orderFactory = $orderFactory = $this->getMockBuilder(
            'Magento\CustomerCustomAttributes\Model\Sales\OrderFactory'
        )->disableOriginalConstructor()->setMethods(['create'])->getMock();
        /** @var OrderFactory $orderFactory */

        $this->orderAddressFactory = $orderAddressFactory = $this->getMockBuilder(
            'Magento\CustomerCustomAttributes\Model\Sales\Order\AddressFactory'
        )->disableOriginalConstructor()->setMethods(['create'])->getMock();
        /** @var OrderAddressFactory $orderAddressFactory */

        $this->quoteFactory = $quoteFactory = $this->getMockBuilder(
            'Magento\CustomerCustomAttributes\Model\Sales\QuoteFactory'
        )->disableOriginalConstructor()->setMethods(['create'])->getMock();
        /** @var QuoteFactory $quoteFactory */

        $this->quoteAddressFactory = $quoteAddressFactory = $this->getMockBuilder(
            'Magento\CustomerCustomAttributes\Model\Sales\Quote\AddressFactory'
        )->disableOriginalConstructor()->setMethods(['create'])->getMock();
        /** @var QuoteAddressFactory $quoteAddressFactory */

        $this->observer = new Observer(
            $helper,
            $orderFactory,
            $orderAddressFactory,
            $quoteFactory,
            $quoteAddressFactory
        );
    }

    public function testSalesQuoteAfterLoad()
    {
        $quoteId = 1;
        $observer = $this->getMockBuilder('Magento\Framework\Event\Observer')
            ->disableOriginalConstructor()
            ->getMock();

        $event = $this->getMockBuilder('Magento\Framework\Event')
            ->setMethods(['getQuote'])
            ->disableOriginalConstructor()
            ->getMock();

        $dataModel = $this->getMockBuilder('Magento\Framework\Model\AbstractModel')
            ->setMethods(['getId', '__wakeup'])
            ->disableOriginalConstructor()
            ->getMock();

        $quote = $this->getMockBuilder('Magento\CustomerCustomAttributes\Model\Sales\Quote')
            ->disableOriginalConstructor()
            ->getMock();

        $dataModel->expects($this->once())->method('getId')->will($this->returnValue($quoteId));
        $observer->expects($this->once())->method('getEvent')->will($this->returnValue($event));
        $event->expects($this->once())->method('getQuote')->will($this->returnValue($dataModel));
        $quote->expects($this->once())->method('load')->with($quoteId)->will($this->returnSelf());
        $quote->expects($this->once())->method('attachAttributeData')->with($dataModel)->will($this->returnSelf());
        $this->quoteFactory->expects($this->once())->method('create')->will($this->returnValue($quote));
        /** @var \Magento\Framework\Event\Observer $observer */

        $this->assertInstanceOf(
            'Magento\CustomerCustomAttributes\Model\Observer',
            $this->observer->salesQuoteAfterLoad($observer)
        );
    }

    public function testSalesQuoteAddressCollectionAfterLoad()
    {
        $items = ['test', 'data'];
        $observer = $this->getMockBuilder('Magento\Framework\Event\Observer')
            ->disableOriginalConstructor()
            ->getMock();

        $event = $this->getMockBuilder('Magento\Framework\Event')
            ->setMethods(['getQuoteAddressCollection'])
            ->disableOriginalConstructor()
            ->getMock();

        $dataModel = $this->getMockBuilder('Magento\Framework\Data\Collection\Db')
            ->setMethods(['getItems', '__wakeup'])
            ->disableOriginalConstructor()
            ->getMock();

        $quoteAddress = $this->getMockBuilder('Magento\CustomerCustomAttributes\Model\Sales\Quote\Address')
            ->disableOriginalConstructor()
            ->getMock();

        $dataModel->expects($this->once())->method('getItems')->will($this->returnValue($items));
        $observer->expects($this->once())->method('getEvent')->will($this->returnValue($event));
        $event->expects($this->once())->method('getQuoteAddressCollection')->will($this->returnValue($dataModel));
        $quoteAddress->expects($this->once())->method('attachDataToEntities')->with($items)->will($this->returnSelf());
        $this->quoteAddressFactory->expects($this->once())->method('create')->will($this->returnValue($quoteAddress));
        /** @var \Magento\Framework\Event\Observer $observer */

        $this->assertInstanceOf(
            'Magento\CustomerCustomAttributes\Model\Observer',
            $this->observer->salesQuoteAddressCollectionAfterLoad($observer)
        );
    }

    public function testSalesQuoteAfterSave()
    {
        $observer = $this->getMockBuilder('Magento\Framework\Event\Observer')
            ->disableOriginalConstructor()
            ->getMock();

        $event = $this->getMockBuilder('Magento\Framework\Event')
            ->setMethods(['getQuote'])
            ->disableOriginalConstructor()
            ->getMock();

        $dataModel = $this->getMockBuilder('Magento\Framework\Model\AbstractModel')
            ->setMethods(['__wakeup'])
            ->disableOriginalConstructor()
            ->getMock();

        $quote = $this->getMockBuilder('Magento\CustomerCustomAttributes\Model\Sales\Quote')
            ->disableOriginalConstructor()
            ->getMock();

        $observer->expects($this->once())->method('getEvent')->will($this->returnValue($event));
        $event->expects($this->once())->method('getQuote')->will($this->returnValue($dataModel));
        $quote->expects($this->once())->method('saveAttributeData')->with($dataModel)->will($this->returnSelf());
        $this->quoteFactory->expects($this->once())->method('create')->will($this->returnValue($quote));
        /** @var \Magento\Framework\Event\Observer $observer */

        $this->assertInstanceOf(
            'Magento\CustomerCustomAttributes\Model\Observer',
            $this->observer->salesQuoteAfterSave($observer)
        );
    }

    public function testSalesQuoteAddressAfterSave()
    {
        $observer = $this->getMockBuilder('Magento\Framework\Event\Observer')
            ->disableOriginalConstructor()
            ->getMock();

        $event = $this->getMockBuilder('Magento\Framework\Event')
            ->setMethods(['getQuoteAddress'])
            ->disableOriginalConstructor()
            ->getMock();

        $dataModel = $this->getMockBuilder('Magento\Framework\Model\AbstractModel')
            ->setMethods(['__wakeup'])
            ->disableOriginalConstructor()
            ->getMock();

        $quoteAddress = $this->getMockBuilder('Magento\CustomerCustomAttributes\Model\Sales\Quote\Address')
            ->disableOriginalConstructor()
            ->getMock();

        $observer->expects($this->once())->method('getEvent')->will($this->returnValue($event));
        $event->expects($this->once())->method('getQuoteAddress')->will($this->returnValue($dataModel));
        $quoteAddress->expects($this->once())->method('saveAttributeData')->with($dataModel)->will($this->returnSelf());
        $this->quoteAddressFactory->expects($this->once())->method('create')->will($this->returnValue($quoteAddress));
        /** @var \Magento\Framework\Event\Observer $observer */

        $this->assertInstanceOf(
            'Magento\CustomerCustomAttributes\Model\Observer',
            $this->observer->salesQuoteAddressAfterSave($observer)
        );
    }

    public function testSalesOrderAfterLoad()
    {
        $orderId = 1;
        $observer = $this->getMockBuilder('Magento\Framework\Event\Observer')
            ->disableOriginalConstructor()
            ->getMock();

        $event = $this->getMockBuilder('Magento\Framework\Event')
            ->setMethods(['getOrder'])
            ->disableOriginalConstructor()
            ->getMock();

        $dataModel = $this->getMockBuilder('Magento\Framework\Model\AbstractModel')
            ->setMethods(['getId', '__wakeup'])
            ->disableOriginalConstructor()
            ->getMock();

        $order = $this->getMockBuilder('Magento\CustomerCustomAttributes\Model\Sales\Order')
            ->disableOriginalConstructor()
            ->getMock();

        $dataModel->expects($this->once())->method('getId')->will($this->returnValue($orderId));
        $observer->expects($this->once())->method('getEvent')->will($this->returnValue($event));
        $event->expects($this->once())->method('getOrder')->will($this->returnValue($dataModel));
        $order->expects($this->once())->method('load')->with($orderId)->will($this->returnSelf());
        $order->expects($this->once())->method('attachAttributeData')->with($dataModel)->will($this->returnSelf());
        $this->orderFactory->expects($this->once())->method('create')->will($this->returnValue($order));
        /** @var \Magento\Framework\Event\Observer $observer */

        $this->assertInstanceOf(
            'Magento\CustomerCustomAttributes\Model\Observer',
            $this->observer->salesOrderAfterLoad($observer)
        );
    }

    public function testSalesOrderAddressCollectionAfterLoad()
    {
        $items = ['test', 'data'];
        $observer = $this->getMockBuilder('Magento\Framework\Event\Observer')
            ->disableOriginalConstructor()
            ->getMock();

        $event = $this->getMockBuilder('Magento\Framework\Event')
            ->setMethods(['getOrderAddressCollection'])
            ->disableOriginalConstructor()
            ->getMock();

        $dataModel = $this->getMockBuilder('Magento\Framework\Data\Collection\Db')
            ->setMethods(['getItems', '__wakeup'])
            ->disableOriginalConstructor()
            ->getMock();

        $orderAddress = $this->getMockBuilder('Magento\CustomerCustomAttributes\Model\Sales\Quote\Address')
            ->disableOriginalConstructor()
            ->getMock();

        $dataModel->expects($this->once())->method('getItems')->will($this->returnValue($items));
        $observer->expects($this->once())->method('getEvent')->will($this->returnValue($event));
        $event->expects($this->once())->method('getOrderAddressCollection')->will($this->returnValue($dataModel));
        $orderAddress->expects($this->once())->method('attachDataToEntities')->with($items)->will($this->returnSelf());
        $this->orderAddressFactory->expects($this->once())->method('create')->will($this->returnValue($orderAddress));
        /** @var \Magento\Framework\Event\Observer $observer */

        $this->assertInstanceOf(
            'Magento\CustomerCustomAttributes\Model\Observer',
            $this->observer->salesOrderAddressCollectionAfterLoad($observer)
        );
    }

    public function testSalesOrderAfterSave()
    {
        $observer = $this->getMockBuilder('Magento\Framework\Event\Observer')
            ->disableOriginalConstructor()
            ->getMock();

        $event = $this->getMockBuilder('Magento\Framework\Event')
            ->setMethods(['getOrder'])
            ->disableOriginalConstructor()
            ->getMock();

        $dataModel = $this->getMockBuilder('Magento\Framework\Model\AbstractModel')
            ->setMethods(['__wakeup'])
            ->disableOriginalConstructor()
            ->getMock();

        $order = $this->getMockBuilder('Magento\CustomerCustomAttributes\Model\Sales\Order')
            ->disableOriginalConstructor()
            ->getMock();

        $observer->expects($this->once())->method('getEvent')->will($this->returnValue($event));
        $event->expects($this->once())->method('getOrder')->will($this->returnValue($dataModel));
        $order->expects($this->once())->method('saveAttributeData')->with($dataModel)->will($this->returnSelf());
        $this->orderFactory->expects($this->once())->method('create')->will($this->returnValue($order));
        /** @var \Magento\Framework\Event\Observer $observer */

        $this->assertInstanceOf(
            'Magento\CustomerCustomAttributes\Model\Observer',
            $this->observer->salesOrderAfterSave($observer)
        );
    }

    public function testSalesOrderAddressAfterLoad()
    {
        $observer = $this->getMockBuilder('Magento\Framework\Event\Observer')
            ->disableOriginalConstructor()
            ->getMock();

        $event = $this->getMockBuilder('Magento\Framework\Event')
            ->setMethods(['getAddress'])
            ->disableOriginalConstructor()
            ->getMock();

        $dataModel = $this->getMockBuilder('Magento\Framework\Model\AbstractModel')
            ->setMethods(['__wakeup'])
            ->disableOriginalConstructor()
            ->getMock();

        $orderAddress = $this->getMockBuilder('Magento\CustomerCustomAttributes\Model\Sales\Order\Address')
            ->disableOriginalConstructor()
            ->getMock();

        $observer->expects($this->once())->method('getEvent')->will($this->returnValue($event));
        $event->expects($this->once())->method('getAddress')->will($this->returnValue($dataModel));
        $orderAddress->expects($this->once())
            ->method('attachDataToEntities')
            ->with([$dataModel])
            ->will($this->returnSelf());
        $this->orderAddressFactory->expects($this->once())->method('create')->will($this->returnValue($orderAddress));
        /** @var \Magento\Framework\Event\Observer $observer */

        $this->assertInstanceOf(
            'Magento\CustomerCustomAttributes\Model\Observer',
            $this->observer->salesOrderAddressAfterLoad($observer)
        );
    }

    public function testSalesOrderAddressAfterSave()
    {
        $observer = $this->getMockBuilder('Magento\Framework\Event\Observer')
            ->disableOriginalConstructor()
            ->getMock();

        $event = $this->getMockBuilder('Magento\Framework\Event')
            ->setMethods(['getAddress'])
            ->disableOriginalConstructor()
            ->getMock();

        $dataModel = $this->getMockBuilder('Magento\Framework\Model\AbstractModel')
            ->setMethods(['__wakeup'])
            ->disableOriginalConstructor()
            ->getMock();

        $orderAddress = $this->getMockBuilder('Magento\CustomerCustomAttributes\Model\Sales\Quote\Address')
            ->disableOriginalConstructor()
            ->getMock();

        $observer->expects($this->once())->method('getEvent')->will($this->returnValue($event));
        $event->expects($this->once())->method('getAddress')->will($this->returnValue($dataModel));
        $orderAddress->expects($this->once())->method('saveAttributeData')->with($dataModel)->will($this->returnSelf());
        $this->orderAddressFactory->expects($this->once())->method('create')->will($this->returnValue($orderAddress));
        /** @var \Magento\Framework\Event\Observer $observer */

        $this->assertInstanceOf(
            'Magento\CustomerCustomAttributes\Model\Observer',
            $this->observer->salesOrderAddressAfterSave($observer)
        );
    }

    public function testEnterpriseCustomerAttributeSave()
    {
        $observer = $this->getMockBuilder('Magento\Framework\Event\Observer')
            ->disableOriginalConstructor()
            ->getMock();

        $event = $this->getMockBuilder('Magento\Framework\Event')
            ->setMethods(['getAttribute'])
            ->disableOriginalConstructor()
            ->getMock();

        $dataModel = $this->getMockBuilder('Magento\Customer\Model\Attribute')
            ->setMethods(['__wakeup', 'isObjectNew'])
            ->disableOriginalConstructor()
            ->getMock();

        $order = $this->getMockBuilder('Magento\CustomerCustomAttributes\Model\Sales\Order')
            ->disableOriginalConstructor()
            ->getMock();

        $quote = $this->getMockBuilder('Magento\CustomerCustomAttributes\Model\Sales\Quote')
            ->disableOriginalConstructor()
            ->getMock();

        $dataModel->expects($this->once())->method('isObjectNew')->will($this->returnValue(true));
        $observer->expects($this->once())->method('getEvent')->will($this->returnValue($event));
        $event->expects($this->once())->method('getAttribute')->will($this->returnValue($dataModel));
        $quote->expects($this->once())->method('saveNewAttribute')->with($dataModel)->will($this->returnSelf());
        $this->quoteFactory->expects($this->once())->method('create')->will($this->returnValue($quote));
        $order->expects($this->once())->method('saveNewAttribute')->with($dataModel)->will($this->returnSelf());
        $this->orderFactory->expects($this->once())->method('create')->will($this->returnValue($order));
        /** @var \Magento\Framework\Event\Observer $observer */

        $this->assertInstanceOf(
            'Magento\CustomerCustomAttributes\Model\Observer',
            $this->observer->enterpriseCustomerAttributeSave($observer)
        );
    }

    public function testEnterpriseCustomerAttributeDelete()
    {
        $observer = $this->getMockBuilder('Magento\Framework\Event\Observer')
            ->disableOriginalConstructor()
            ->getMock();

        $event = $this->getMockBuilder('Magento\Framework\Event')
            ->setMethods(['getAttribute'])
            ->disableOriginalConstructor()
            ->getMock();

        $dataModel = $this->getMockBuilder('Magento\Customer\Model\Attribute')
            ->setMethods(['__wakeup', 'isObjectNew'])
            ->disableOriginalConstructor()
            ->getMock();

        $order = $this->getMockBuilder('Magento\CustomerCustomAttributes\Model\Sales\Order')
            ->disableOriginalConstructor()
            ->getMock();

        $quote = $this->getMockBuilder('Magento\CustomerCustomAttributes\Model\Sales\Quote')
            ->disableOriginalConstructor()
            ->getMock();

        $dataModel->expects($this->once())->method('isObjectNew')->will($this->returnValue(false));
        $observer->expects($this->once())->method('getEvent')->will($this->returnValue($event));
        $event->expects($this->once())->method('getAttribute')->will($this->returnValue($dataModel));
        $quote->expects($this->once())->method('deleteAttribute')->with($dataModel)->will($this->returnSelf());
        $this->quoteFactory->expects($this->once())->method('create')->will($this->returnValue($quote));
        $order->expects($this->once())->method('deleteAttribute')->with($dataModel)->will($this->returnSelf());
        $this->orderFactory->expects($this->once())->method('create')->will($this->returnValue($order));
        /** @var \Magento\Framework\Event\Observer $observer */

        $this->assertInstanceOf(
            'Magento\CustomerCustomAttributes\Model\Observer',
            $this->observer->enterpriseCustomerAttributeDelete($observer)
        );
    }

    public function testEnterpriseCustomerAddressAttributeSave()
    {
        $observer = $this->getMockBuilder('Magento\Framework\Event\Observer')
            ->disableOriginalConstructor()
            ->getMock();

        $event = $this->getMockBuilder('Magento\Framework\Event')
            ->setMethods(['getAttribute'])
            ->disableOriginalConstructor()
            ->getMock();

        $dataModel = $this->getMockBuilder('Magento\Customer\Model\Attribute')
            ->setMethods(['__wakeup', 'isObjectNew'])
            ->disableOriginalConstructor()
            ->getMock();

        $orderAddress = $this->getMockBuilder('Magento\CustomerCustomAttributes\Model\Sales\Order\Address')
            ->disableOriginalConstructor()
            ->getMock();

        $quoteAddress = $this->getMockBuilder('Magento\CustomerCustomAttributes\Model\Sales\Quote\Address')
            ->disableOriginalConstructor()
            ->getMock();

        $dataModel->expects($this->once())->method('isObjectNew')->will($this->returnValue(true));
        $observer->expects($this->once())->method('getEvent')->will($this->returnValue($event));
        $event->expects($this->once())->method('getAttribute')->will($this->returnValue($dataModel));
        $quoteAddress->expects($this->once())->method('saveNewAttribute')->with($dataModel)->will($this->returnSelf());
        $this->quoteAddressFactory->expects($this->once())->method('create')->will($this->returnValue($quoteAddress));
        $orderAddress->expects($this->once())->method('saveNewAttribute')->with($dataModel)->will($this->returnSelf());
        $this->orderAddressFactory->expects($this->once())->method('create')->will($this->returnValue($orderAddress));
        /** @var \Magento\Framework\Event\Observer $observer */

        $this->assertInstanceOf(
            'Magento\CustomerCustomAttributes\Model\Observer',
            $this->observer->enterpriseCustomerAddressAttributeSave($observer)
        );
    }

    public function testEnterpriseCustomerAddressAttributeDelete()
    {
        $observer = $this->getMockBuilder('Magento\Framework\Event\Observer')
            ->disableOriginalConstructor()
            ->getMock();

        $event = $this->getMockBuilder('Magento\Framework\Event')
            ->setMethods(['getAttribute'])
            ->disableOriginalConstructor()
            ->getMock();

        $dataModel = $this->getMockBuilder('Magento\Customer\Model\Attribute')
            ->setMethods(['__wakeup', 'isObjectNew'])
            ->disableOriginalConstructor()
            ->getMock();

        $orderAddress = $this->getMockBuilder('Magento\CustomerCustomAttributes\Model\Sales\Order\Address')
            ->disableOriginalConstructor()
            ->getMock();

        $quoteAddress = $this->getMockBuilder('Magento\CustomerCustomAttributes\Model\Sales\Quote\Address')
            ->disableOriginalConstructor()
            ->getMock();

        $dataModel->expects($this->once())->method('isObjectNew')->will($this->returnValue(false));
        $observer->expects($this->once())->method('getEvent')->will($this->returnValue($event));
        $event->expects($this->once())->method('getAttribute')->will($this->returnValue($dataModel));
        $quoteAddress->expects($this->once())->method('deleteAttribute')->with($dataModel)->will($this->returnSelf());
        $this->quoteAddressFactory->expects($this->once())->method('create')->will($this->returnValue($quoteAddress));
        $orderAddress->expects($this->once())->method('deleteAttribute')->with($dataModel)->will($this->returnSelf());
        $this->orderAddressFactory->expects($this->once())->method('create')->will($this->returnValue($orderAddress));
        /** @var \Magento\Framework\Event\Observer $observer */

        $this->assertInstanceOf(
            'Magento\CustomerCustomAttributes\Model\Observer',
            $this->observer->enterpriseCustomerAddressAttributeDelete($observer)
        );
    }

    public function coreCopyMethodsDataProvider()
    {
        return [
            'coreCopyFieldsetSalesConvertQuoteToOrder' => [
                'coreCopyFieldsetSalesConvertQuoteToOrder',
                'getCustomerUserDefinedAttributeCodes',
                'customer_',
                'customer_',
            ],
            'coreCopyFieldsetSalesCopyOrderToEdit' => [
                'coreCopyFieldsetSalesCopyOrderToEdit',
                'getCustomerUserDefinedAttributeCodes',
                'customer_',
                'customer_',
            ],
            'coreCopyFieldsetCustomerAccountToQuote' => [
                'coreCopyFieldsetCustomerAccountToQuote',
                'getCustomerUserDefinedAttributeCodes',
                '',
                'customer_',
            ],
            'coreCopyFieldsetCheckoutOnepageQuoteToCustomer' => [
                'coreCopyFieldsetCheckoutOnepageQuoteToCustomer',
                'getCustomerUserDefinedAttributeCodes',
                'customer_',
                '',
            ],
            'coreCopyFieldsetSalesConvertQuoteAddressToOrderAddress' => [
                'coreCopyFieldsetSalesConvertQuoteAddressToOrderAddress',
                'getCustomerAddressUserDefinedAttributeCodes',
                '',
                '',
            ],
            'coreCopyFieldsetSalesCopyOrderBillingAddressToOrder' => [
                'coreCopyFieldsetSalesCopyOrderBillingAddressToOrder',
                'getCustomerAddressUserDefinedAttributeCodes',
                '',
                '',
            ],
            'coreCopyFieldsetSalesCopyOrderShippingAddressToOrder' => [
                'coreCopyFieldsetSalesCopyOrderShippingAddressToOrder',
                'getCustomerAddressUserDefinedAttributeCodes',
                '',
                '',
            ],
            'coreCopyFieldsetCustomerAddressToQuoteAddress' => [
                'coreCopyFieldsetCustomerAddressToQuoteAddress',
                'getCustomerAddressUserDefinedAttributeCodes',
                '',
                '',
            ],
            'coreCopyFieldsetQuoteAddressToCustomerAddress' => [
                'coreCopyFieldsetQuoteAddressToCustomerAddress',
                'getCustomerAddressUserDefinedAttributeCodes',
                '',
                '',
            ],
        ];
    }

    /**
     * @test
     * @dataProvider coreCopyMethodsDataProvider
     *
     * @param string $testableMethod
     * @param string $helperMethod
     * @param string $sourcePrefix
     * @param string $targetPrefix
     */
    public function testCoreCopyMethods($testableMethod, $helperMethod, $sourcePrefix, $targetPrefix)
    {
        $attribute = 'testAttribute';
        $attributeData = 'data';
        $attributes = [$attribute];
        $sourceAttributeWithPrefix = $sourcePrefix . $attribute;
        $targetAttributeWithPrefix = $targetPrefix . $attribute;

        $observer = $this->getMockBuilder('Magento\Framework\Event\Observer')
            ->disableOriginalConstructor()
            ->getMock();

        $event = $this->getMockBuilder('Magento\Framework\Event')
            ->setMethods(['getSource', 'getTarget'])
            ->disableOriginalConstructor()
            ->getMock();

        $sourceModel = $this->getMockBuilder('Magento\Framework\Model\AbstractModel')
            ->setMethods(['__wakeup', 'getData'])
            ->disableOriginalConstructor()
            ->getMock();

        $targetModel = $this->getMockBuilder('Magento\Framework\Model\AbstractModel')
            ->setMethods(['__wakeup', 'setData'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->helper->expects($this->once())
            ->method($helperMethod)
            ->will($this->returnValue($attributes));
        $sourceModel->expects($this->once())
            ->method('getData')
            ->with($sourceAttributeWithPrefix)
            ->will($this->returnValue($attributeData));
        $targetModel->expects($this->once())
            ->method('setData')
            ->with($this->logicalOr($targetAttributeWithPrefix, $attributeData))
            ->will($this->returnSelf());
        $observer->expects($this->exactly(2))->method('getEvent')->will($this->returnValue($event));
        $event->expects($this->once())->method('getSource')->will($this->returnValue($sourceModel));
        $event->expects($this->once())->method('getTarget')->will($this->returnValue($targetModel));
        /** @var \Magento\Framework\Event\Observer $observer */

        $this->assertInstanceOf(
            'Magento\CustomerCustomAttributes\Model\Observer',
            $this->observer->$testableMethod($observer)
        );
    }

    /**
     * @expectedException \Magento\Eav\Exception
     */
    public function testEnterpriseCustomerAttributeBeforeSaveNegative()
    {
        $attributeData = 'so_long_attribute_code_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx';
        $observer = $this->getMockBuilder('Magento\Framework\Event\Observer')
            ->disableOriginalConstructor()
            ->getMock();

        $event = $this->getMockBuilder('Magento\Framework\Event')
            ->setMethods(['getAttribute'])
            ->disableOriginalConstructor()
            ->getMock();

        $dataModel = $this->getMockBuilder('Magento\Customer\Model\Attribute')
            ->setMethods(['__wakeup', 'isObjectNew', 'getAttributeCode'])
            ->disableOriginalConstructor()
            ->getMock();

        $dataModel->expects($this->once())
            ->method('isObjectNew')
            ->will($this->returnValue(true));

        $dataModel->expects($this->once())
            ->method('getAttributeCode')
            ->will($this->returnValue($attributeData));

        $observer->expects($this->once())->method('getEvent')->will($this->returnValue($event));
        $event->expects($this->once())->method('getAttribute')->will($this->returnValue($dataModel));
        /** @var \Magento\Framework\Event\Observer $observer */

        $this->observer->enterpriseCustomerAttributeBeforeSave($observer);
    }

    public function testEnterpriseCustomerAttributeBeforeSavePositive()
    {
        $attributeData = 'normal_attribute_code';
        $observer = $this->getMockBuilder('Magento\Framework\Event\Observer')
            ->disableOriginalConstructor()
            ->getMock();

        $event = $this->getMockBuilder('Magento\Framework\Event')
            ->setMethods(['getAttribute'])
            ->disableOriginalConstructor()
            ->getMock();

        $dataModel = $this->getMockBuilder('Magento\Customer\Model\Attribute')
            ->setMethods(['__wakeup', 'isObjectNew', 'getAttributeCode'])
            ->disableOriginalConstructor()
            ->getMock();

        $dataModel->expects($this->once())
            ->method('isObjectNew')
            ->will($this->returnValue(true));

        $dataModel->expects($this->once())
            ->method('getAttributeCode')
            ->will($this->returnValue($attributeData));

        $observer->expects($this->once())->method('getEvent')->will($this->returnValue($event));
        $event->expects($this->once())->method('getAttribute')->will($this->returnValue($dataModel));
        /** @var \Magento\Framework\Event\Observer $observer */

        $this->assertInstanceOf(
            'Magento\CustomerCustomAttributes\Model\Observer',
            $this->observer->enterpriseCustomerAttributeBeforeSave($observer)
        );
    }
}
