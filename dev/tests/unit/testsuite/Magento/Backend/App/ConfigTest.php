<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\App;

class ConfigTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Core\Model\Config\SectionPool|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $sectionPool;

    /**
     * @var Config
     */
    protected $model;

    protected function setUp()
    {
        $this->sectionPool = $this->getMock(
            'Magento\Core\Model\Config\SectionPool',
            array('getSection', 'clean'),
            array(),
            '',
            false
        );
        $this->model = new Config($this->sectionPool);
    }

    public function testGetValue()
    {
        $expectedValue = 'some value';
        $path = 'some path';
        $configData = $this->getConfigDataMock('getValue');
        $configData
            ->expects($this->once())
            ->method('getValue')
            ->with($this->equalTo($path))
            ->will($this->returnValue($expectedValue));
        $this->sectionPool
            ->expects($this->once())
            ->method('getSection')
            ->with($this->equalTo('default'), $this->isNull())
            ->will($this->returnValue($configData));
        $this->assertEquals($expectedValue, $this->model->getValue($path));
    }

    public function testSetValue()
    {
        $value = 'some value';
        $path = 'some path';
        $configData = $this->getConfigDataMock('setValue');
        $configData
            ->expects($this->once())
            ->method('setValue')
            ->with($this->equalTo($path), $this->equalTo($value));
        $this->sectionPool
            ->expects($this->once())
            ->method('getSection')
            ->with($this->equalTo('default'), $this->isNull())
            ->will($this->returnValue($configData));
        $this->model->setValue($path, $value);
    }

    public function testReinit()
    {
        $this->sectionPool->expects($this->once())->method('clean');
        $this->model->reinit();
    }

    /**
     * @param mixed $configValue
     * @param bool $expectedResult
     * @dataProvider getFlagDataProvider
     */
    public function testGetFlag($configValue, $expectedResult)
    {
        $path = 'some path';
        $configData = $this->getConfigDataMock('getValue');
        $configData
            ->expects($this->once())
            ->method('getValue')
            ->with($this->equalTo($path))
            ->will($this->returnValue($configValue));
        $this->sectionPool
            ->expects($this->once())
            ->method('getSection')
            ->with($this->equalTo('default'), $this->isNull())
            ->will($this->returnValue($configData));
        $this->assertEquals($expectedResult, $this->model->isSetFlag($path));
    }

    public function getFlagDataProvider()
    {
        return array(
            array(0, false),
            array(true, true),
            array('0', false),
            array('', false),
            array('some string', true),
            array(1, true),
        );
    }

    /**
     * Get ConfigData mock
     *
     * @param $mockedMethod
     * @return \PHPUnit_Framework_MockObject_MockObject|\Magento\Core\Model\Config\Data
     */
    protected function getConfigDataMock($mockedMethod)
    {
        return $this->getMock('Magento\Core\Model\Config\Data', array($mockedMethod), array(), '', false);
    }
}
