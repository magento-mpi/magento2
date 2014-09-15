<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Service\V1\Action;

/**
 * Class CreditmemoCancelTest
 */
class CreditmemoCancelTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Sales\Service\V1\Action\CreditmemoCancel
     */
    protected $creditmemoCancel;

    /**
     * @var \Magento\Sales\Model\Order\CreditmemoRepository|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $creditmemoRepositoryMock;

    /**
     * @var \Magento\Sales\Model\Order\Creditmemo|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $creditmemoMock;

    /**
     * SetUp
     */
    protected function setUp()
    {
        $this->creditmemoRepositoryMock = $this->getMock(
            'Magento\Sales\Model\Order\CreditmemoRepository',
            ['get'],
            [],
            '',
            false
        );
        $this->creditmemoMock = $this->getMock(
            'Magento\Sales\Model\Order\Creditmemo',
            [],
            [],
            '',
            false
        );
        $this->creditmemoCancel = new CreditmemoCancel(
            $this->creditmemoRepositoryMock
        );
    }

    /**
     * test creditmemo cancel service
     */
    public function testInvoke()
    {
        $this->creditmemoRepositoryMock->expects($this->once())
            ->method('get')
            ->with($this->equalTo(1))
            ->will($this->returnValue($this->creditmemoMock));
        $this->creditmemoMock->expects($this->once())
            ->method('cancel')
            ->will($this->returnSelf());
        $this->assertTrue($this->creditmemoCancel->invoke(1));
    }
}
