<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Rma\Service\V1;

use Magento\TestFramework\Helper\ObjectManager as ObjectManagerHelper;

class TrackReadTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Rma\Service\V1\TrackRead | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $rmaServiceTrackReadMock;

    /**
     * @var \Magento\Rma\Model\RmaRepository | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $rmaRepositoryMock;

    /**
     * @var \Magento\Rma\Service\V1\Data\TrackBuilder | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $rmaDataTrackBuilderMock;

    /**
     * @var \Magento\Rma\Model\Shipping\LabelService | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $rmaLabelServiceMock;

    /**
     * @var \Magento\Rma\Service\V1\Data\Rma | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $dataRmaMock;

    /**
     * @var \Magento\Rma\Model\Rma | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $rmaModelMock;

    protected function setUp()
    {
        $this->rmaRepositoryMock = $this->getMockBuilder('Magento\Rma\Model\RmaRepository')
            ->disableOriginalConstructor()
            ->setMethods(['get'])
            ->getMock();

        $this->rmaDataTrackBuilderMock = $this->getMockBuilder('Magento\Rma\Service\V1\Data\TrackBuilder')
            ->disableOriginalConstructor()
            ->setMethods(['populateWithArray', 'create'])
            ->getMock();

        $this->rmaLabelServiceMock = $this->getMockBuilder('Magento\Rma\Model\Shipping\LabelService')
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();

        $this->rmaModelMock = $this->getMockBuilder('Magento\Rma\Model\Rma')
            ->disableOriginalConstructor()
            ->setMethods(['__wakeup', 'getTrackingNumbers', 'getId'])
            ->getMock();

        $this->dataRmaMock = $this->getMockBuilder('Magento\Rma\Service\V1\Data\Rma')
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();

        $this->rmaServiceTrackReadMock = (new ObjectManagerHelper($this))->getObject(
            'Magento\Rma\Service\V1\TrackRead',
            [
                "repository" => $this->rmaRepositoryMock,
                "trackBuilder" => $this->rmaDataTrackBuilderMock,
                "labelService" => $this->rmaLabelServiceMock
            ]
        );
    }

    /**
     *
     */
    public function testGetTracks()
    {
        $id = 1;

        $trackDataArray = ['entity_id' => 1];

        $trackMock = $this->getMockBuilder('Magento\Rma\Model\Shipping')
            ->disableOriginalConstructor()
            ->setMethods(['__wakeup', 'getData'])
            ->getMock();

        $trackDataMock = $this->getMockBuilder('Magento\Rma\Service\V1\Data\Track')
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();

        $trackMock->expects($this->once())->method('getData')
            ->willReturn($trackDataArray);

        $this->rmaModelMock->expects($this->once())->method('getTrackingNumbers')
            ->willReturn([$trackMock]);

        $this->rmaRepositoryMock->expects($this->once())->method('get')
            ->with($id)
            ->willReturn($this->rmaModelMock);

        $this->rmaDataTrackBuilderMock->expects($this->once())->method('populateWithArray')
            ->with($trackDataArray);

        $this->rmaDataTrackBuilderMock->expects($this->once())->method('create')
            ->willReturn($trackDataMock);

        $this->assertEquals(
            [$trackDataMock],
            $this->rmaServiceTrackReadMock->getTracks($id)
        );
    }


    /**
     * @dataProvider getShippingLabelPdfDataProvider
     *
     * @param integer $id                 Rma model ID
     * @param bool    $doesRmaModelExists Does Rma model exist
     * @param string  $pdfData            Some test data
     */
    public function testGetShippingLabelPdf($id, $doesRmaModelExists, $pdfData)
    {
        $this->rmaRepositoryMock->expects($this->once())->method('get')
            ->with($id)
            ->willReturn($this->rmaModelMock);

        $this->rmaModelMock->expects($this->once())->method('getId')
            ->willReturn($doesRmaModelExists);

        $this->rmaLabelServiceMock->expects($this->exactly((int)$doesRmaModelExists))
            ->method('getShippingLabelByRmaPdf')
            ->with($this->rmaModelMock)
            ->willReturn($pdfData);

        $this->assertEquals(
            base64_encode($pdfData),
            $this->rmaServiceTrackReadMock->getShippingLabelPdf($id)
        );
    }

    /**
     * Data provider of success cases
     *
     * @see testGetShippingLabelPdf
     * @return array
     *
     * @case #1 We got Magento\Rma\Model\Rma with some id and return some PDF-data
     * @case #2 We didn't get Magento\Rma\Model\Rma with some id and return empty string
     *
     */
    public function getShippingLabelPdfDataProvider()
    {
        return [
            1 => [1, true, 'blabla'],
            2 => [1, false, '']
        ];
    }
}
 