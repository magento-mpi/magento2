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
    protected $converter;

    protected function setUp()
    {
        $this->converter = $this->getMock(
            'Magento\Rma\Model\Rma\Converter',
            ['getPreparedModelData', 'createNewRmaModel', 'getModel'],
            [],
            '',
            false
        );
        $this->objectManagerHelper = new ObjectManagerHelper($this);
        $this->rmaService = $this->objectManagerHelper->getObject(
            'Magento\Rma\Service\V1\RmaWrite',
            ['converter' => $this->converter]
        );
    }

    /**
     * @dataProvider createDataProvider
     */
    public function testCreate($preparedRmaData, $expected)
    {
        $rmaModel = $this->getMock('Magento\Rma\Model\Rma', ['saveRma'], [], '', false);
        $rmaDataObject = $this->getMock('Magento\Rma\Service\V1\Data\Rma', [], [], '', false);
        $this->converter->expects($this->once())
            ->method('getPreparedModelData')
            ->with($rmaDataObject)
            ->willReturn($preparedRmaData);
        $this->converter->expects($this->once())
            ->method('createNewRmaModel')
            ->with($rmaDataObject, $preparedRmaData)
            ->willReturn($rmaModel);
        $rmaModel->expects($this->once())
            ->method('saveRma')
            ->with($preparedRmaData)
            ->willReturn($expected);

        $this->assertEquals($expected, $this->rmaService->create($rmaDataObject));
    }

    /**
     * @return array
     */
    public function createDataProvider()
    {
        return [
            [['entity_id' => 1], true],
            [['entity_id' => 1], false]
        ];
    }

    /**
     * @dataProvider updateDataProvider
     */
    public function testUpdate($id, $preparedRmaData, $expected)
    {
        $rmaModel = $this->getMock('Magento\Rma\Model\Rma', ['saveRma'], [], '', false);
        $rmaDataObject = $this->getMock('Magento\Rma\Service\V1\Data\Rma', [], [], '', false);
        $this->converter->expects($this->once())
            ->method('getPreparedModelData')
            ->with($rmaDataObject)
            ->willReturn($preparedRmaData);
        $this->converter->expects($this->once())
            ->method('getModel')
            ->with($id, $preparedRmaData)
            ->willReturn($rmaModel);
        $rmaModel->expects($this->once())
            ->method('saveRma')
            ->with($preparedRmaData)
            ->willReturn($expected);

        $this->assertEquals($expected, $this->rmaService->update($id, $rmaDataObject));
    }

    /**
     * @return array
     */
    public function updateDataProvider()
    {
        return [
            [1, ['entity_id' => 1], true],
            [1, ['entity_id' => 1], false]
        ];
    }
}
