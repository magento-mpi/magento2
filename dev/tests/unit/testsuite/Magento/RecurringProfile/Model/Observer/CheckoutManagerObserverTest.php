<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\RecurringProfile\Model\Observer;

class CheckoutManagerObserverTest extends \PHPUnit_Framework_TestCase
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

        $this->_testModel = $helper->getObject('Magento\RecurringProfile\Model\Observer\CheckoutManagerObserver', [
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
        $this->_quote->expects($this->once())->method('import')
            ->will($this->returnValue([$this->_profile]));
        $this->_profile->expects($this->once())->method('isValid')->will($this->returnValue(true));
        $this->_profile->expects($this->once())->method('submit');

        $this->_testModel->submitRecurringPaymentProfiles($this->_observer);

        $this->_testModel->addRecurringProfileIdsToSession();
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

        $this->_event->expects($this->any())->method('getQuote')->will($this->returnValue($quote));
    }
}
