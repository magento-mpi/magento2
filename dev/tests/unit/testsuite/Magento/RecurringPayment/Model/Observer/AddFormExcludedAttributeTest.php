<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\RecurringPayment\Model\Observer;

class AddFormExcludedAttributeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\Event\Observer
     */
    protected $_observer;

    /**
     * @var \Magento\RecurringPayment\Model\Observer\AddFormExcludedAttribute
     */
    protected $_testModel;


    /**
     * @var \Magento\Framework\Event
     */
    protected $_event;

    /**
     * @var \Magento\RecurringPayment\Model\Payment
     */
    protected $_payment;

    protected function setUp()
    {
        $this->_observer = $this->getMock('Magento\Framework\Event\Observer', array(), array(), '', false);

        $this->_testModel = new \Magento\RecurringPayment\Model\Observer\AddFormExcludedAttribute();

        $this->_event = $this->getMock(
            'Magento\Framework\Event',
            array('getProductElement', 'getProduct', 'getResult', 'getBuyRequest', 'getQuote', 'getApi', 'getObject'),
            array(),
            '',
            false
        );

        $this->_observer->expects($this->any())->method('getEvent')->will($this->returnValue($this->_event));
    }

    public function testExecute()
    {
        $block = $this->getMock(
            'Magento\Backend\Block\Template',
            array('getFormExcludedFieldList', 'setFormExcludedFieldList'),
            array(),
            '',
            false
        );
        $block->expects($this->once())->method('getFormExcludedFieldList')->will($this->returnValue(array('field')));
        $block->expects($this->once())->method('setFormExcludedFieldList')->with(array('field', 'recurring_payment'));

        $this->_event->expects($this->once())->method('getObject')->will($this->returnValue($block));
        $this->_testModel->execute($this->_observer);
    }
} 
