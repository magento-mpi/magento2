<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\RecurringPayment\Model;

class ObserverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Event\Observer
     */
    protected $_observer;

    /**
     * @var \Magento\RecurringPayment\Model\Observer
     */
    protected $_testModel;

    /**
     * @var \Magento\RecurringPayment\Block\Fields
     */
    protected $_fieldsBlock;

    /**
     * @var \Magento\RecurringPayment\Model\RecurringPaymentFactory
     */
    protected $_recurringPaymentFactory;

    /**
     * @var \Magento\Event
     */
    protected $_event;

    /**
     * @var \Magento\RecurringPayment\Model\PaymentFactory
     */
    protected $_paymentFactory;

    /**
     * @var \Magento\RecurringPayment\Model\Payment
     */
    protected $_payment;

    protected function setUp()
    {
        $this->_observer = $this->getMock('Magento\Event\Observer', [], [], '', false);
        $this->_fieldsBlock = $this->getMock(
            '\Magento\RecurringPayment\Block\Fields', ['getFieldLabel'], [], '', false
        );
        $this->_recurringPaymentFactory = $this->getMock(
            '\Magento\RecurringPayment\Model\RecurringPaymentFactory', ['create'], [], '', false
        );
        $this->_paymentFactory = $this->getMock(
            '\Magento\RecurringPayment\Model\PaymentFactory', ['create', 'importProduct'], [], '', false
        );

        $helper = new \Magento\TestFramework\Helper\ObjectManager($this);

        $this->_testModel = $helper->getObject('Magento\RecurringPayment\Model\Observer', [
            'recurringPaymentFactory' => $this->_recurringPaymentFactory,
            'fields' => $this->_fieldsBlock,
            'paymentFactory' => $this->_paymentFactory
        ]);

        $this->_event = $this->getMock(
            'Magento\Event', [
                'getProductElement', 'getProduct', 'getResult', 'getBuyRequest', 'getQuote', 'getApi', 'getObject'
            ], [], '', false
        );

        $this->_observer->expects($this->any())->method('getEvent')->will($this->returnValue($this->_event));
        $this->_payment = $this->getMock('Magento\RecurringPayment\Model\Payment', [
            '__sleep', '__wakeup', 'isValid', 'importQuote', 'importQuoteItem', 'submit', 'getId', 'setMethodCode'
        ], [], '', false);
    }

    public function testPrepareProductRecurringPaymentOptions()
    {
        $payment = $this->getMock(
            'Magento\Object',
            [
                'setStory',
                'importBuyRequest',
                'importProduct',
                'exportStartDatetime',
                'exportScheduleInfo',
                'getFieldLabel'
            ],
            [],
            '',
            false
        );
        $payment->expects($this->once())->method('exportStartDatetime')->will($this->returnValue('date'));
        $payment->expects($this->any())->method('setStore')->will($this->returnValue($payment));
        $payment->expects($this->once())->method('importBuyRequest')->will($this->returnValue($payment));
        $payment->expects($this->once())->method('exportScheduleInfo')
            ->will($this->returnValue([new \Magento\Object(['title' => 'Title', 'schedule' => 'schedule'])]));

        $this->_fieldsBlock->expects($this->once())->method('getFieldLabel')->will($this->returnValue('Field Label'));

        $this->_recurringPaymentFactory->expects($this->once())->method('create')->will($this->returnValue($payment));

        $product = $this->getMock('Magento\Object', ['getIsRecurring', 'addCustomOption'], [], '', false);
        $product->expects($this->once())->method('getIsRecurring')->will($this->returnValue(true));

        $infoOptions = [
            ['label' => 'Field Label', 'value' => 'date'],
            ['label' => 'Title', 'value' => 'schedule']
        ];

        $product->expects($this->at(2))->method('addCustomOption')->with(
            'additional_options',
            serialize($infoOptions)
        );

        $this->_event->expects($this->any())->method('getProduct')->will($this->returnValue($product));

        $this->_testModel->prepareProductRecurringPaymentOptions($this->_observer);
    }

    public function testAddFormExcludedAttribute()
    {
        $block = $this->getMock('Magento\Backend\Block\Template', [
            'getFormExcludedFieldList', 'setFormExcludedFieldList'
        ], [], '', false);
        $block->expects($this->once())->method('getFormExcludedFieldList')->will($this->returnValue(['field']));
        $block->expects($this->once())->method('setFormExcludedFieldList')->with(['field', 'recurring_payment']);

        $this->_event->expects($this->once())->method('getObject')->will($this->returnValue($block));
        $this->_testModel->addFormExcludedAttribute($this->_observer);
    }
}
