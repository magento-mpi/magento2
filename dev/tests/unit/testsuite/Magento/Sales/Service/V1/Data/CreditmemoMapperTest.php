<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Service\V1\Data;

/**
 * Class CreditmemoMapperTest
 */
class CreditmemoMapperTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Sales\Service\V1\Data\CreditmemoMapper
     */
    protected $creditmemoMapper;

    /**
     * @var \Magento\Sales\Service\V1\Data\CreditmemoBuilder|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $creditmemoBuilderMock;

    /**
     * @var \Magento\Sales\Service\V1\Data\CreditmemoItemMapper|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $creditmemoItemMapperMock;

    /**
     * @var \Magento\Sales\Model\Order\Creditmemo|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $creditmemoMock;

    /**
     * @var \Magento\Sales\Model\Order\Creditmemo\Item|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $creditmemoItemMock;

    /**
     * SetUp
     *
     * @return void
     */
    protected function setUp()
    {
        $this->creditmemoBuilderMock = $this->getMock(
            'Magento\Sales\Service\V1\Data\CreditmemoBuilder',
            ['populateWithArray', 'setItems', 'create'],
            [],
            '',
            false
        );
        $this->creditmemoItemMapperMock = $this->getMock(
            'Magento\Sales\Service\V1\Data\CreditmemoItemMapper',
            ['extractDto'],
            [],
            '',
            false
        );
        $this->creditmemoMock = $this->getMock(
            'Magento\Sales\Model\Order\Creditmemo',
            ['getAllItems', 'getData', '__wakeup'],
            [],
            '',
            false
        );
        $this->creditmemoItemMock = $this->getMock(
            'Magento\Sales\Model\Order\Creditmemo\Item',
            [],
            [],
            '',
            false
        );
        $this->creditmemoMapper = new \Magento\Sales\Service\V1\Data\CreditmemoMapper(
            $this->creditmemoBuilderMock,
            $this->creditmemoItemMapperMock
        );
    }

    /**
     * Run creditmemo mapper test
     *
     * @return void
     */
    public function testInvoke()
    {
        $this->creditmemoMock->expects($this->once())
            ->method('getData')
            ->will($this->returnValue(['field-1' => 'value-1']));
        $this->creditmemoMock->expects($this->once())
            ->method('getAllItems')
            ->will($this->returnValue([$this->creditmemoItemMock]));
        $this->creditmemoBuilderMock->expects($this->once())
            ->method('populateWithArray')
            ->with($this->equalTo(['field-1' => 'value-1']))
            ->will($this->returnSelf());
        $this->creditmemoItemMapperMock->expects($this->once())
            ->method('extractDto')
            ->with($this->equalTo($this->creditmemoItemMock))
            ->will($this->returnValue('item-1'));
        $this->creditmemoBuilderMock->expects($this->once())
            ->method('setItems')
            ->with($this->equalTo(['item-1']))
            ->will($this->returnSelf());
        $this->creditmemoBuilderMock->expects($this->once())
            ->method('create')
            ->will($this->returnValue('data-object-with-creditmemo'));
        $this->assertEquals('data-object-with-creditmemo', $this->creditmemoMapper->extractDto($this->creditmemoMock));
    }
}
