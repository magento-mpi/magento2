<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Ui\ContentType\Builders;

/**
 * Class ConfigStorageJsonTest
 */
class ConfigStorageJsonTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ConfigStorageJson
     */
    protected $builder;

    public function testToJson()
    {
        $this->builder = new ConfigStorageJson();
        $name = 'name';
        $data = [];
        $parentName = 'parentName';
        $result = [
            'config' => ['components' => [$name => $data], 'globalData' => ['globalData']],
            'meta' => null,
            'name' => $name,
            'parent_name' => $parentName,
            'data' => null,
            'dump' => ['extenders' => []]
        ];

        $rootComponentMock = $this->getMock(
            'Magento\Ui\Configuration',
            ['getName', 'getParentName', 'getData'],
            [],
            '',
            false
        );
        $storageMock = $this->getMock(
            'Magento\Ui\ConfigurationStorage',
            ['getComponentsData', 'getGlobalData', 'getMeta', 'getData'],
            [],
            '',
            false
        );

        $storageMock->expects($this->once())
            ->method('getComponentsData')
            ->with($parentName)
            ->will($this->returnValue($rootComponentMock));
        $rootComponentMock->expects($this->any())->method('getName')->willReturn($result['name']);
        $rootComponentMock->expects($this->once())->method('getParentName')->willReturn($result['parent_name']);
        $rootComponentMock->expects($this->once())
            ->method('getData')
            ->willReturn($data);
        $storageMock->expects($this->once())->method('getGlobalData')->willReturn($result['config']);

        $this->assertEquals(json_encode($result), $this->builder->toJson($storageMock, $parentName));
    }

    public function testToJsonNoParentName()
    {
        $this->builder = new ConfigStorageJson();
        $data = [];
        $result = [
            'config' => ['components' => ['name' => $data], 'globalData' => ['globalData']],
            'meta' => null,
            'data' => null,
            'dump' => ['extenders' => []]
        ];
        $componentsMock = $this->getMock('Magento\Ui\Configuration', ['getData'], [], '', false);
        $storageMock = $this->getMock(
            'Magento\Ui\ConfigurationStorage',
            ['getComponentsData', 'getGlobalData', 'getMeta', 'getData'],
            [],
            '',
            false
        );

        $storageMock->expects($this->once())->method('getComponentsData')->will($this->returnValue($componentsMock));
        $componentsMock->expects($this->any())->method('getData')->willReturn($data);

        $storageMock->expects($this->once())->method('getMeta')->will($this->returnValue($result['meta']));
        $storageMock->expects($this->once())->method('getData')->will($this->returnValue($result['data']));
        $storageMock->expects($this->once())->method('getGlobalData')->willReturn($result['config']);

        $this->assertEquals(json_encode($result), $this->builder->toJson($storageMock));
    }
}
