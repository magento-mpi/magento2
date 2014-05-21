<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Rma\Model;

use Magento\Rma\Model\Rma\HistoryPlugin;
use Magento\Rma\Model\Rma\Status\History;
use Magento\Rma\Model\Rma;
use Magento\Framework\App\RequestInterface;
use Magento\Rma\Model\Rma\Status\HistoryFactory;

/**
 * Class HistoryPluginTest
 */
class HistoryPluginTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var HistoryPlugin
     */
    protected $plugin;

    /**
     * @var RequestInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $requestMock;

    /**
     * @var HistoryFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $historyFactoryMock;

    /**
     * @var History|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $historyMock;

    /**
     * @var Rma|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $rmaMock;

    public function setUp()
    {
        $this->requestMock = $this->getMock(
            'Magento\Framework\App\RequestInterface',
            ['getParam'],
            [],
            '',
            false,
            false,
            false
        );
        $this->historyFactoryMock = $this->getMock(
            'Magento\Rma\Model\Rma\Status\HistoryFactory',
            ['create'],
            [],
            '',
            false
        );
        $this->historyMock = $this->getMock(
            'Magento\Rma\Model\Rma\Status\History',
            ['__wakeup', 'setRma', 'setIsCustomerNotified', 'saveSystemComment'],
            [],
            '',
            false
        );
        $this->rmaMock = $this->getMock(
            'Magento\Rma\Model\Rma',
            [],
            [],
            '',
            false
        );
        $this->plugin = new \Magento\Rma\Model\Rma\HistoryPlugin(
            $this->historyFactoryMock,
            $this->requestMock
        );
    }

    /**
     * test beforeSaveRma method
     */
    public function testBeforeSaveRma()
    {
        $expected = ['some-input-data'];
        $this->requestMock->expects($this->once())
            ->method('getParam')
            ->with($this->equalTo(HistoryPlugin::RMA_CONFIRMATION))
            ->will($this->returnValue(true));
        $this->assertEquals([$expected], $this->plugin->beforeSaveRma($this->rmaMock, $expected));
    }

    /**
     * test afterSave method (with beforeSave method)
     */
    public function testAfterSave()
    {
        $this->testBeforeSaveRma();
        $expected = ['some-output-data'];
        $this->historyFactoryMock->expects($this->once())
            ->method('create')
            ->will($this->returnValue($this->historyMock));
        $this->historyMock->expects($this->once())
            ->method('setRma')
            ->with($this->equalTo($this->rmaMock))
            ->will($this->returnSelf());
        $this->historyMock->expects($this->once())
            ->method('setIsCustomerNotified')
            ->with($this->equalTo(true))
            ->will($this->returnSelf());
        $this->historyMock->expects($this->once())
            ->method('saveSystemComment')
            ->will($this->returnSelf());
        $this->assertEquals([$expected], $this->plugin->afterSave($this->rmaMock, $expected));
    }
}
