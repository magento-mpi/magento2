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

    /**
     * Test for removeTrackById
     *
     * @dataProvider removeTrackByIdDataProvider
     *
     * @param integer $id                 Rma model id
     * @param integer $trackId            Track id
     * @param boolean $doesRmaModelExists Does Rma Model exists
     * @param boolean $removeTrackResult  Expected remove track method result
     */
    public function testRemoveTrackById($id, $trackId, $doesRmaModelExists, $removeTrackResult)
    {
        $this->permissionCheckerMock->expects($this->once())->method('isCustomerContext')
            ->willReturn(false);

        $this->rmaModelMock->expects($this->once())->method('getId')
            ->willReturn($doesRmaModelExists);

        $this->rmaRepositoryMock->expects($this->once())->method('get')
            ->with($id)
            ->willReturn($this->rmaModelMock);

        $this->rmaLabelServiceMock->expects($this->exactly((int)$doesRmaModelExists))
            ->method('removeTrack')
            ->with($trackId)
            ->willReturn($removeTrackResult);

        $this->assertEquals(
            $removeTrackResult,
            $this->rmaServiceTrackWriteMock->removeTrackById($id, $trackId)
        );
    }

    /**
     * DataProvider for testRemoveTrackById
     *
     * @see testRemoveTrackById
     * @return array
     *
     * @case #1 Rma model exists
     * @case #2 Rma model doesn't exists
     */
    public function removeTrackByIdDataProvider()
    {
        return [
            1 => [1, 1, true, 1],
            2 => [1, 1, false, 0]
        ];
    }

    /**
     * Test for addTrack
     *
     * @dataProvider addTrackDataProvider
     *
     * @param integer $id                 Rma model id
     * @param integer $trackNumber        Track number
     * @param string  $carrierCode        Carrier code
     * @param string  $carrierTitle       Carrier title
     * @param boolean $doesRmaModelExists Does Rma Model exists
     * @param boolean $addTrackResult     Expected add track method result
     */
    public function testAddTrack($id, $trackNumber, $carrierCode, $carrierTitle, $doesRmaModelExists, $addTrackResult)
    {
        $this->permissionCheckerMock->expects($this->once())->method('isCustomerContext')
            ->willReturn(false);

        $this->rmaModelMock->expects($this->at(0))->method('getId')
            ->willReturn($doesRmaModelExists);

        if ($doesRmaModelExists) {
            $this->rmaModelMock->expects($this->at(1))->method('getId')
                ->willReturn($doesRmaModelExists);
        }

        /** @var \Magento\Rma\Service\V1\Data\Track | \PHPUnit_Framework_MockObject_MockObject $trackMock */
        $trackMock = $this->getMockBuilder('Magento\Rma\Service\V1\Data\Track')
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();

        $trackMock->expects($this->exactly((int)$doesRmaModelExists))->method('getTrackNumber')
            ->willReturn($trackNumber);

        $trackMock->expects($this->exactly((int)$doesRmaModelExists))->method('getCarrierCode')
            ->willReturn($carrierCode);

        $trackMock->expects($this->exactly((int)$doesRmaModelExists))->method('getCarrierTitle')
            ->willReturn($carrierTitle);

        $this->rmaRepositoryMock->expects($this->once())->method('get')
            ->with($id)
            ->willReturn($this->rmaModelMock);

        $this->rmaLabelServiceMock->expects($this->exactly((int)$doesRmaModelExists))
            ->method('addTrack')
            ->with($id, $trackNumber, $carrierCode, $carrierTitle)
            ->willReturn($addTrackResult);

        $this->assertEquals(
            $addTrackResult,
            $this->rmaServiceTrackWriteMock->addTrack($id, $trackMock)
        );
    }

    /**
     * DataProvider for testAddTrack
     *
     * @see testAddTrack
     * @return array
     *
     * @case #1 Rma model exists
     * @case #2 Rma model doesn't exists
     */
    public function addTrackDataProvider()
    {
        return [
            1 => [1, 1, 'carrierCode', 'carrierTitle', true, true],
            2 => [1, 1, 'carrierCode', 'carrierTitle', false, false]
        ];
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
 
