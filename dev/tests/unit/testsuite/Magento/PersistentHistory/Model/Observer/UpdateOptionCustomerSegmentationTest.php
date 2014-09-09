<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\PersistentHistory\Model\Observer;

class UpdateOptionCustomerSegmentationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $valueFactoryMock;

    /**
     * @var \Magento\PersistentHistory\Model\Observer\UpdateOptionCustomerSegmentation
     */
    protected $subject;

    protected function setUp()
    {
        /** @var \Magento\TestFramework\Helper\ObjectManager */
        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->valueFactoryMock = $this->getMock(
            '\Magento\Framework\App\Config\ValueFactory',
            ['create'],
            [],
            '',
            false
        );
        $this->subject = $objectManager->getObject(
            '\Magento\PersistentHistory\Model\Observer\UpdateOptionCustomerSegmentation',
            ['valueFactory' => $this->valueFactoryMock]
        );
    }

    public function testUpdateOptionIfEventValueIsNull()
    {
        $observerMock = $this->getMock('\Magento\Framework\Event\Observer', ['getEvent'], [], '', false);
        $eventDataObjectMock = $this->getMock(
            '\Magento\PersistentHistory\Model\Adminhtml\System\Config\Cart',
            ['getValue', '__wakeup'],
            [],
            '',
            false
        );

        $eventDataObjectMock->expects($this->once())
            ->method('getValue')
            ->will($this->returnValue(null));

        $eventMock = $this->getMock('\Magento\Framework\Event', ['getDataObject'], [], '', false);
        $eventMock->expects($this->once())->method('getDataObject')->will($this->returnValue($eventDataObjectMock));

        $observerMock->expects($this->once())->method('getEvent')->will($this->returnValue($eventMock));
        $this->subject->execute($observerMock);
    }

    public function testUpdateOptionSuccess()
    {
        $scopeId = 1;
        $scope = ['scope' => 'scope_value'];

        $observerMock = $this->getMock('\Magento\Framework\Event\Observer', ['getEvent'], [], '', false);
        $eventDataObjectMock = $this->getMock(
            '\Magento\PersistentHistory\Model\Adminhtml\System\Config\Cart',
            ['getValue', '__wakeup', 'getScope', 'getScopeId'],
            [],
            '',
            false
        );

        $eventDataObjectMock->expects($this->once())
            ->method('getValue')
            ->will($this->returnValue('value'));
        $eventDataObjectMock->expects($this->once())
            ->method('getScope')
            ->will($this->returnValue($scope));
        $eventDataObjectMock->expects($this->once())
            ->method('getScopeId')
            ->will($this->returnValue($scopeId));

        $eventMock = $this->getMock('\Magento\Framework\Event', ['getDataObject'], [], '', false);
        $eventMock->expects($this->once())->method('getDataObject')->will($this->returnValue($eventDataObjectMock));

        $observerMock->expects($this->once())->method('getEvent')->will($this->returnValue($eventMock));

        $valueMock = $this->getMock(
            '\Magento\Framework\App\Config\Value',
            ['setScope', 'setScopeId', 'setValue', 'save', 'setPath', '__wakeup'],
            [],
            '',
            false
        );
        $this->valueFactoryMock->expects($this->once())->method('create')->will($this->returnValue($valueMock));

        $valueMock->expects($this->once())->method('setScope')->with($scope)->will($this->returnSelf());
        $valueMock->expects($this->once())->method('setScopeId')->with($scopeId)->will($this->returnSelf());
        $valueMock->expects($this->once())->method('setValue')->with(true)->will($this->returnSelf());
        $valueMock->expects($this->once())->method('save')->will($this->returnSelf());
        $valueMock->expects($this->once())->method('setPath')
            ->with(\Magento\PersistentHistory\Helper\Data::XML_PATH_PERSIST_CUSTOMER_AND_SEGM)
            ->will($this->returnSelf());

        $this->subject->execute($observerMock);
    }
}
 