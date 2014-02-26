<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\RecurringProfile\Model;

class ObserverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Event\Observer
     */
    protected $_observer;

    /**
     * @var \Magento\RecurringProfile\Model\Observer
     */
    protected $_testModel;

    /**
     * @var \Magento\RecurringProfile\Block\Fields
     */
    protected $_fieldsBlock;

    /**
     * @var \Magento\RecurringProfile\Model\RecurringProfileFactory
     */
    protected $_recurringProfileFactory;

    /**
     * @var \Magento\Event
     */
    protected $_event;

    /**
     * @var \Magento\RecurringProfile\Model\ProfileFactory
     */
    protected $_profileFactory;

    /**
     * @var \Magento\RecurringProfile\Model\Profile
     */
    protected $_profile;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;

    protected $_quote;

    protected function setUp()
    {
        $this->_observer = $this->getMock('Magento\Event\Observer', [], [], '', false);
        $this->_fieldsBlock = $this->getMock(
            '\Magento\RecurringProfile\Block\Fields', ['getFieldLabel'], [], '', false
        );
        $this->_recurringProfileFactory = $this->getMock(
            '\Magento\RecurringProfile\Model\RecurringProfileFactory', ['create'], [], '', false
        );
        $this->_profileFactory = $this->getMock(
            '\Magento\RecurringProfile\Model\ProfileFactory', ['create', 'importProduct'], [], '', false
        );
        $this->_checkoutSession = $this->getMock(
            '\Magento\Checkout\Model\Session', ['setLastRecurringProfileIds'], [], '', false
        );
        $this->_quote = $this->getMock(
            '\Magento\RecurringProfile\Model\QuoteImporter',
            ['import'],
            [],
            '',
            false
        );

        $helper = new \Magento\TestFramework\Helper\ObjectManager($this);

        $this->_testModel = $helper->getObject('Magento\RecurringProfile\Model\Observer', [
            'recurringProfileFactory' => $this->_recurringProfileFactory,
            'fields' => $this->_fieldsBlock,
            'profileFactory' => $this->_profileFactory,
            'checkoutSession' => $this->_checkoutSession,
            'quoteImporter' => $this->_quote
        ]);

        $this->_event = $this->getMock(
            'Magento\Event', [
                'getProductElement', 'getProduct', 'getResult', 'getBuyRequest', 'getQuote', 'getApi', 'getObject'
            ], [], '', false
        );

        $this->_observer->expects($this->any())->method('getEvent')->will($this->returnValue($this->_event));
        $this->_profile = $this->getMock('Magento\RecurringProfile\Model\Profile', [
            '__sleep', '__wakeup', 'isValid', 'importQuote', 'importQuoteItem', 'submit', 'getId', 'setMethodCode'
        ], [], '', false);
    }

    public function testPrepareProductRecurringProfileOptions()
    {
        $profile = $this->getMock(
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
        $profile->expects($this->once())->method('exportStartDatetime')->will($this->returnValue('date'));
        $profile->expects($this->any())->method('setStore')->will($this->returnValue($profile));
        $profile->expects($this->once())->method('importBuyRequest')->will($this->returnValue($profile));
        $profile->expects($this->once())->method('exportScheduleInfo')
            ->will($this->returnValue([new \Magento\Object(['title' => 'Title', 'schedule' => 'schedule'])]));

        $this->_fieldsBlock->expects($this->once())->method('getFieldLabel')->will($this->returnValue('Field Label'));

        $this->_recurringProfileFactory->expects($this->once())->method('create')->will($this->returnValue($profile));

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

        $this->_testModel->prepareProductRecurringProfileOptions($this->_observer);
    }

    public function testSubmitRecurringPaymentProfiles()
    {
        $this->_prepareRecurringPaymentProfiles();
        $this->_quote->expects($this->once())->method('import')
            ->will($this->returnValue([$this->_profile]));

        $this->_profile->expects($this->once())->method('isValid')->will($this->returnValue(true));
        $this->_profile->expects($this->once())->method('submit');

        $this->_testModel->submitRecurringPaymentProfiles($this->_observer);
    }

    public function testAddRecurringProfileIdsToSession()
    {
        $this->_prepareRecurringPaymentProfiles();

        $this->_testModel->addRecurringProfileIdsToSession($this->_observer);
    }

    public function testAddFormExcludedAttribute()
    {
        $block = $this->getMock('Magento\Backend\Block\Template', [
            'getFormExcludedFieldList', 'setFormExcludedFieldList'
        ], [], '', false);
        $block->expects($this->once())->method('getFormExcludedFieldList')->will($this->returnValue(['field']));
        $block->expects($this->once())->method('setFormExcludedFieldList')->with(['field', 'recurring_profile']);

        $this->_event->expects($this->once())->method('getObject')->will($this->returnValue($block));
        $this->_testModel->addFormExcludedAttribute($this->_observer);
    }

    protected function _prepareRecurringPaymentProfiles()
    {
        $product = $this->getMock('Magento\RecurringProfile\Model\Profile', [
            'getIsRecurring', '__sleep', '__wakeup'
        ], [], '', false);
        $product->expects($this->any())->method('getIsRecurring')->will($this->returnValue(true));

        $this->_profile = $this->getMock('Magento\RecurringProfile\Model\Profile', [
            '__sleep', '__wakeup', 'isValid', 'importQuote', 'importQuoteItem', 'submit', 'getId', 'setMethodCode'
        ], [], '', false);

        $quote = $this->getMock('Magento\Sales\Model\Quote', [
            'getTotalsCollectedFlag', '__sleep', '__wakeup', 'getAllVisibleItems'
        ], [], '', false);

        $this->_event->expects($this->once())->method('getQuote')->will($this->returnValue($quote));
    }
}
