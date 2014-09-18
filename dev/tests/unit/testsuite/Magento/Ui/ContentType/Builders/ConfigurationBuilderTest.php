<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Ui\ContentType\Builders;

class ConfigurationBuilderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ConfigurationBuilder
     */
    protected $builder;

    public function testToJson()
    {
        $this->builder = new ConfigurationBuilder();
        $result = ['name' => 'resultName', 'parent_name' => 'resultParentName'];
        $configurationMock = $this->getMock(
            'Magento\Ui\Configuration',
            ['getData', 'getName', 'getParentName'],
            [],
            '',
            false);
        $configurationMock->expects($this->once())->method('getData')->willReturn($result);
        $configurationMock->expects($this->once())->method('getName')->willReturn($result['name']);
        $configurationMock->expects($this->once())->method('getParentName')->willReturn($result['parent_name']);
        $this->assertEquals(json_encode($result), $this->builder->toJson($configurationMock));
    }
}
