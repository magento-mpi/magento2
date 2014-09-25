<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Rma\Service\V1;

use Magento\TestFramework\Helper\ObjectManager as ObjectManagerHelper;

class TrackWriteTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Rma\Service\V1\TrackWrite | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $rmaServiceTrackWriteMock;

    /**
     * @var \Magento\Rma\Model\Shipping\LabelService | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $rmaLabelServiceMock;
    /**
     * @var \Magento\Rma\Model\RmaRepository | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $rmaRepositoryMock;
    /**
     * @var \Magento\Rma\Model\Rma\PermissionChecker | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $permissionCheckerMock;

    /**
     * @var \Magento\Rma\Model\Rma | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $rmaModelMock;

    /**
     * Sets up the Mocks.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->rmaLabelServiceMock = $this->getMockBuilder('Magento\Rma\Model\Shipping\LabelService')
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();

        $this->rmaRepositoryMock = $this->getMockBuilder('Magento\Rma\Model\RmaRepository')
            ->disableOriginalConstructor()
            ->setMethods(['__wakeup', 'get'])
            ->getMock();

        $this->permissionCheckerMock = $this->getMockBuilder('Magento\Rma\Model\Rma\PermissionChecker')
            ->disableOriginalConstructor()
            ->setMethods(['__wakeup', 'isCustomerContext'])
            ->getMock();

        $this->rmaModelMock = $this->getMockBuilder('Magento\Rma\Model\Rma')
            ->disableOriginalConstructor()
            ->setMethods(['__wakeup', 'getId'])
            ->getMock();

        $this->rmaServiceTrackWriteMock = (new ObjectManagerHelper($this))->getObject(
            'Magento\Rma\Service\V1\TrackWrite',
            [
                "labelService"      => $this->rmaLabelServiceMock,
                "rmaRepository"     => $this->rmaRepositoryMock,
                "permissionChecker" => $this->permissionCheckerMock,
            ]
        );
    }

    public function testRemoveTrackById()
    {
        list($id, $trackId) = [1, 1];
        $this->permissionCheckerMock->expects($this->once())->method('isCustomerContext')
            ->willReturn(false);

        $this->rmaModelMock->expects($this->once())->method('getId')
            ->willReturn($id);

        $this->rmaRepositoryMock->expects($this->once())->method('get')
            ->with($id)
            ->willReturn($this->rmaModelMock);

        $this->rmaLabelServiceMock->expects($this->once())
            ->method('removeTrack')
            ->with($trackId)
            ->willReturn(true);

        $this->assertTrue($this->rmaServiceTrackWriteMock->removeTrackById($id, $trackId));
    }

    public function testAddTrack()
    {
        list ($id, $trackNumber, $carrierCode, $carrierTitle, $addTrackResult) = [1, 1, 'code', 'title', true];
        $this->permissionCheckerMock->expects($this->once())->method('isCustomerContext')
            ->willReturn(false);

        $this->rmaModelMock->expects($this->once())->method('getId')
            ->willReturn($id);

        /** @var \Magento\Rma\Service\V1\Data\Track | \PHPUnit_Framework_MockObject_MockObject $trackMock */
        $trackMock = $this->getMockBuilder('Magento\Rma\Service\V1\Data\Track')
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();

        $trackMock->expects($this->once())->method('getTrackNumber')
            ->willReturn($trackNumber);

        $trackMock->expects($this->once())->method('getCarrierCode')
            ->willReturn($carrierCode);

        $trackMock->expects($this->once())->method('getCarrierTitle')
            ->willReturn($carrierTitle);

        $this->rmaRepositoryMock->expects($this->once())->method('get')
            ->with($id)
            ->willReturn($this->rmaModelMock);

        $this->rmaLabelServiceMock->expects($this->once())
            ->method('addTrack')
            ->with($id, $trackNumber, $carrierCode, $carrierTitle)
            ->willReturn($addTrackResult);

        $this->assertEquals(
            $addTrackResult,
            $this->rmaServiceTrackWriteMock->addTrack($id, $trackMock)
        );
    }

    /**
     * @expectedException        \Magento\Framework\Exception\StateException
     * @expectedExceptionMessage Unknown service
     */
    public function testAddTrackException()
    {
        $this->permissionCheckerMock->expects($this->once())->method('isCustomerContext')
            ->willReturn(true);

        /** @var \Magento\Rma\Service\V1\Data\Track $trackDataMock */
        $trackDataMock = $this->getMockBuilder('Magento\Rma\Service\V1\Data\Track')
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();

        $this->rmaServiceTrackWriteMock->addTrack(1, $trackDataMock);
    }

    /**
     * @expectedException        \Magento\Framework\Exception\StateException
     * @expectedExceptionMessage Unknown service
     */
    public function testRemoveTrackByIdException()
    {
        $this->permissionCheckerMock->expects($this->once())->method('isCustomerContext')
            ->willReturn(true);

        $this->rmaServiceTrackWriteMock->removeTrackById(1, 1);
    }
}
