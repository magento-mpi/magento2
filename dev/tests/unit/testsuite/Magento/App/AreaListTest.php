<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\App;

class AreaListTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\App\AreaList
     */
    protected $_model;

    /**
     * @var \Magento\App\Area\FrontNameResolverFactory
     */
    protected $_resolverFactory;

    protected function setUp()
    {
        $this->_resolverFactory = $this->getMock(
            '\Magento\App\Area\FrontNameResolverFactory',
            array(),
            array(),
            '',
            false
        );
    }

    public function testGetCodeByFrontNameWhenAreaDoesNotContainFrontName()
    {
        $expected = 'expectedFrontName';
        $this->_model = new \Magento\App\AreaList(
            $this->_resolverFactory,
            array('testArea' => array('frontNameResolver' => 'testValue')),
            $expected
        );

        $resolverMock = $this->getMock('\Magento\App\Area\FrontNameResolverInterface');
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
        $this->_model = new \Magento\App\AreaList(
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
        $this->_model = new \Magento\App\AreaList(
            $this->_resolverFactory,
            array('testAreaCode' => array('frontName' => 'testFrontName')),
            $expected
        );

        $actual = $this->_model->getFrontName('testAreaCode');
        $this->assertEquals($expected, $actual);
    }

    public function testGetFrontNameWhenAreaCodeAndFrontNameArentSet()
    {
        $this->_model = new \Magento\App\AreaList($this->_resolverFactory, array(), '');

        $actual = $this->_model->getFrontName('testAreaCode');
        $this->assertNull($actual);
    }

    public function testGetCodes()
    {
        $this->_model = new \Magento\App\AreaList(
            $this->_resolverFactory,
            array('area1' => 'value1', 'area2' => 'value2'),
            ''
        );

        $expected = array(0 => 'area1', 1 => 'area2');
        $actual = $this->_model->getCodes();
        $this->assertEquals($expected, $actual);
    }
}
