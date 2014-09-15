<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Rma\Service\V1;

use Magento\TestFramework\Helper\ObjectManager as ObjectManagerHelper;

class RmaWriteTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\Rma\Service\V1\RmaWrite */
    protected $rmaService;

    /** @var ObjectManagerHelper */
    protected $objectManagerHelper;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $rmaRepository;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $labelService;

    protected function setUp()
    {
        $this->rmaRepository = $this->getMock('Magento\Rma\Model\RmaRepository', ['get'], [], '', false);
        $this->labelService = $this->getMock(
            'Magento\Rma\Model\Shipping\LabelService',
            ['addTrack', 'removeTrack'],
            [],
            '',
            false
        );
        $this->objectManagerHelper = new ObjectManagerHelper($this);
        $this->rmaService = $this->objectManagerHelper->getObject(
            'Magento\Rma\Service\V1\RmaWrite',
            [
                'rmaRepository' => $this->rmaRepository,
                'labelService' => $this->labelService
            ]
        );
    }

    /**
     * @dataProvider addTrackDataProvider
     */
    public function testAddTrack($id, $number, $carrier, $title, $isAdmin, $expected)
    {
        $rmaModel = $this->getMock('Magento\Rma\Model\Rma', ['getId'], [], '', false);
        $rmaModel->expects($this->any())
            ->method('getId')
            ->willReturn($id);
        $this->rmaRepository->expects($this->once())
            ->method('get')
            ->willReturn($rmaModel);
        $this->labelService->expects($this->any())
            ->method('addTrack')
            ->willReturn($expected);
        $this->assertEquals($expected, $this->rmaService->addTrack($id, $number, $carrier, $title, $isAdmin));
    }

    /**
     * @return array
     */
    public function addTrackDataProvider()
    {
        return [
            [1, '123qwer', '', '', null, true],
            [1, '123qwer', 'some_carrier', '', null, true],
            [1, '123qwer', 'some_carrier', '', 3, true],
            [1, '123qwer', '', 'Some Title', null, true],
            [1, '123qwer', 'some_carrier', 'Some Title', null, true],
            [1, '123qwer', 'some_carrier', 'Some Title', 3, true],
            [0, '123qwer', 'some_carrier', 'Some Title', 3, false],
        ];
    }

    /**
     * @dataProvider removeTrackByIdDataProvider
     */
    public function testRemoveTrackById($id, $trackId, $expected)
    {
        $rmaModel = $this->getMock('Magento\Rma\Model\Rma', ['getId'], [], '', false);
        $rmaModel->expects($this->any())
            ->method('getId')
            ->willReturn($id);
        $this->rmaRepository->expects($this->once())
            ->method('get')
            ->willReturn($rmaModel);
        $this->labelService->expects($this->any())
            ->method('removeTrack')
            ->willReturn($expected);
        $this->assertEquals($expected, $this->rmaService->removeTrackById($id, $trackId));
    }

    /**
     * @return array
     */
    public function removeTrackByIdDataProvider()
    {
        return [
            [1, 1, true],
            [0, 1, false],
        ];
    }
}
