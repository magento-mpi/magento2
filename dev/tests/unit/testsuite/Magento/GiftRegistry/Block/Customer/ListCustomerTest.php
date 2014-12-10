<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\GiftRegistry\Block\Customer;

class ListCustomerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\GiftRegistry\Block\Customer\ListCustomer
     */
    protected $block;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $contextMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $localeDateMock;

    protected function setUp()
    {
        $helper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->contextMock = $this->getMock('Magento\Framework\View\Element\Template\Context', [], [], '', false);
        $this->localeDateMock = $this->getMock('Magento\Framework\Stdlib\DateTime\TimezoneInterface');
        $this->contextMock
            ->expects($this->any())
        ->method('getLocaleDate')
        ->will($this->returnValue($this->localeDateMock));
        $this->block = $helper->getObject('Magento\GiftRegistry\Block\Customer\ListCustomer',
            ['context' => $this->contextMock]
        );
    }

    public function testGetFormattedDate()
    {
        $date = '07/24/14';
        $itemMock = $this->getMock('\Magento\GiftRegistry\Model\Entity', ['getCreatedAt', '__wakeup'], [], '', false);
        $itemMock->expects($this->once())->method('getCreatedAt')->will($this->returnValue($date));
        $this->localeDateMock
            ->expects($this->any())
            ->method('date')
            ->with(strtotime($date), null, null, false)
            ->will($this->returnValue($date));
        $this->localeDateMock
            ->expects($this->once())
            ->method('formatDate')
            ->with($date, \Magento\Framework\Stdlib\DateTime\TimezoneInterface::FORMAT_TYPE_MEDIUM)
            ->will($this->returnValue($date));
        $this->assertEquals($date, $this->block->getFormattedDate($itemMock));
    }
}
