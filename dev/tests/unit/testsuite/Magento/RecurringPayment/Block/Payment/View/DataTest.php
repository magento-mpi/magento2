<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\RecurringPayment\Block\Payment\View;

/**
 * Test class for \Magento\RecurringPayment\Block\Payment\View\Data
 */
class DataTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\RecurringPayment\Block\Payment\View\Data
     */
    protected $_block;

    /**
     * @var \Magento\RecurringPayment\Model\Payment
     */
    protected $_payment;

    public function testPrepareLayout()
    {
        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);

        $this->_payment = $this->getMockBuilder(
            'Magento\RecurringPayment\Model\Payment'
        )->disableOriginalConstructor()->setMethods(
            array('setStore', 'canFetchUpdate', '__wakeup')
        )->getMock();
        $this->_payment->expects($this->once())->method('setStore')->will($this->returnValue($this->_payment));

        $registry = $this->getMockBuilder(
            'Magento\Registry'
        )->disableOriginalConstructor()->setMethods(
            array('registry')
        )->getMock();
        $registry->expects(
            $this->once()
        )->method(
            'registry'
        )->with(
            'current_recurring_payment'
        )->will(
            $this->returnValue($this->_payment)
        );

        $store = $this->getMockBuilder('Magento\Store\Model\Store')->disableOriginalConstructor()->getMock();

        $storeManager = $this->getMockBuilder(
            'Magento\Store\Model\StoreManager'
        )->disableOriginalConstructor()->setMethods(
            array('getStore')
        )->getMock();
        $storeManager->expects($this->once())->method('getStore')->will($this->returnValue($store));

        $this->_block = $objectManager->getObject(
            'Magento\RecurringPayment\Block\Payment\View\Data',
            array('registry' => $registry, 'storeManager' => $storeManager)
        );

        $layout = $this->getMockBuilder(
            'Magento\Framework\View\Layout'
        )->disableOriginalConstructor()->setMethods(
            array('getParentName', 'getBlock')
        )->getMock();

        $this->assertEmpty($this->_block->getData());
        $this->_block->setLayout($layout);
        $this->assertNotEmpty($this->_block->getData());
    }
}
