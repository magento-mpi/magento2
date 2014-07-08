<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Eav\Model\Attribute\Data;

class AbstractDataTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Eav\Model\Attribute\Data\AbstractData
     */
    protected $model;

    protected function setUp()
    {
        $timezoneMock = $this->getMock('\Magento\Framework\Stdlib\DateTime\TimezoneInterface');
        $loggerMock = $this->getMock('\Magento\Framework\Logger', [], [], '', false);
        $localeResolverMock = $this->getMock('\Magento\Framework\Locale\ResolverInterface');
        $stringMock = $this->getMock('\Magento\Framework\Stdlib\String', [], [], '', false);

        /* testing abstract model through its child */
        $this->model = new Text($timezoneMock, $loggerMock, $localeResolverMock, $stringMock);
    }

    /**
     * @covers \Magento\Eav\Model\Attribute\Data\AbstractData::getEntity
     * @covers \Magento\Eav\Model\Attribute\Data\AbstractData::setEntity
     */
    public function testGetEntity()
    {
        $entityMock = $this->getMock('\Magento\Framework\Model\AbstractModel', [], [], '', false);
        $this->model->setEntity($entityMock);
        $this->assertEquals($entityMock, $this->model->getEntity());
    }

    /**
     * @expectedException \Magento\Framework\Model\Exception
     * @expectedExceptionMessage Entity object is undefined
     *
     * @covers \Magento\Eav\Model\Attribute\Data\AbstractData::getEntity
     */
    public function testGetEntityWhenEntityNotSet()
    {
        $this->model->getEntity();
    }

    /**
     * @covers \Magento\Eav\Model\Attribute\Data\AbstractData::getExtractedData
     * @covers \Magento\Eav\Model\Attribute\Data\AbstractData::setExtractedData
     *
     * @param string $index
     * @param mixed $expectedResult
     *
     * @dataProvider extractedDataDataProvider
     */
    public function testGetExtractedData($index, $expectedResult)
    {
        $extractedData = ['index' => 'value', 'otherIndex' => 'otherValue'];
        $this->model->setExtractedData($extractedData);
        $this->assertEquals($expectedResult, $this->model->getExtractedData($index));
    }

    /**
     * @return array
     */
    public function extractedDataDataProvider()
    {
        return [
            [
                'index' => 'index',
                'expectedResult' => 'value'
            ],
            [
                'index' => null,
                'expectedResult' => ['index' => 'value', 'otherIndex' => 'otherValue']
            ],
            [
                'index' => 'customIndex',
                'expectedResult' => null
            ]
        ];
    }
}
