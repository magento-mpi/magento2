<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\App;

class AreaListTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\App\AreaList
     */
    protected $_model;

    /**
     * @var \Magento\Framework\App\Area\FrontNameResolverFactory
     */
    protected $_resolverFactory;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $objectManagerMock;

    protected function setUp()
    {
        $this->objectManagerMock = $this->getMock('Magento\Framework\ObjectManager');
        $this->_resolverFactory = $this
            ->getMock('\Magento\Framework\App\Area\FrontNameResolverFactory', array(), array(), '', false);
    }

    public function testGetCodeByFrontNameWhenAreaDoesNotContainFrontName()
    {
        $expected = 'expectedFrontName';
        $this->_model = new \Magento\Framework\App\AreaList(
            $this->objectManagerMock,
            $this->_resolverFactory,
            array('testArea' => array('frontNameResolver' => 'testValue')),
            $expected
        );

        $resolverMock = $this->getMock('\Magento\Framework\App\Area\FrontNameResolverInterface');
        $this->_resolverFactory->expects(
            $this->any()
        )->method(
                'create'
            )->with(
                'testValue'
            )->will(
                $this->returnValue($resolverMock)
            );

        $actual = $this->_model->getCodeByFrontName('testFrontName');
        $this->assertEquals($expected, $actual);
    }

    public function testGetCodeByFrontNameReturnsAreaCode()
    {
        $expected = 'testArea';
        $this->_model = new \Magento\Framework\App\AreaList(
            $this->objectManagerMock,
            $this->_resolverFactory,
            array('testArea' => array('frontName' => 'testFrontName')),
            $expected
        );

        $actual = $this->_model->getCodeByFrontName('testFrontName');
        $this->assertEquals($expected, $actual);
    }

    public function testGetFrontNameWhenAreaCodeAndFrontNameAreSet()
    {
        $expected = 'testFrontName';
        $this->_model = new \Magento\Framework\App\AreaList(
            $this->objectManagerMock,
            $this->_resolverFactory,
            array('testAreaCode' => array('frontName' => 'testFrontName')),
            $expected
        );

        $actual = $this->_model->getFrontName('testAreaCode');
        $this->assertEquals($expected, $actual);
    }

    public function testGetFrontNameWhenAreaCodeAndFrontNameArentSet()
    {
        $model = new \Magento\Framework\App\AreaList(
            $this->objectManagerMock,
            $this->_resolverFactory,
            array(),
            ''
        );
        $code = 'testAreaCode';
        $this->assertNull($model->getCodeByFrontName($code));
        $this->assertNull($model->getFrontName($code));
        $this->assertSame([], $model->getCodes());
        $this->assertNull($model->getDefaultRouter($code));
        $this->objectManagerMock->expects($this->once())
            ->method('create')
            ->with('Magento\Framework\App\AreaInterface', ['areaCode' => $code])
            ->willReturn('test');
        $this->assertSame('test', $model->getArea($code));
    }

    public function testGetCodes()
    {
        $areas = array('area1' => 'value1', 'area2' => 'value2');
        $this->_model = new \Magento\Framework\App\AreaList(
            $this->objectManagerMock, $this->_resolverFactory, $areas, ''
        );

        $expected = array_keys($areas);
        $actual = $this->_model->getCodes();
        $this->assertEquals($expected, $actual);
    }

    public function testGetDefaultRouter()
    {
        $areas = array('area1' => ['router' => 'value1'], 'area2' => 'value2');
        $this->_model = new \Magento\Framework\App\AreaList(
            $this->objectManagerMock, $this->_resolverFactory, $areas, ''
        );

        $this->assertEquals($this->_model->getDefaultRouter('area1'), $areas['area1']['router']);
        $this->assertNull($this->_model->getDefaultRouter('area2'));
    }

    public function testGetArea()
    {
        /** @var \Magento\Framework\ObjectManager $objectManagerMock */
        $objectManagerMock = $this->getObjectManagerMockGetArea();
        $areas = array('area1' => ['router' => 'value1'], 'area2' => 'value2');
        $this->_model = new AreaList(
            $objectManagerMock, $this->_resolverFactory, $areas, ''
        );

        $this->assertEquals($this->_model->getArea('testArea'), 'ok');
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getObjectManagerMockGetArea()
    {
        $objectManagerMock = $this->getMock('Magento\Framework\ObjectManager', [], [], '', false);
        $objectManagerMock
            ->expects($this->any())
            ->method('create')
            ->with(
                $this->equalTo('Magento\Framework\App\AreaInterface'),
                $this->equalTo(array('areaCode' => 'testArea'))
            )
            ->will($this->returnValue('ok'));

        return $objectManagerMock;
    }
}
