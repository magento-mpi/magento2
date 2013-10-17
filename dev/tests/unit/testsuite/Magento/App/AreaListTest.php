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
    protected $_frontNameResolverFactory;

    protected function setUp()
    {
        $this->_frontNameResolverFactory = $this
            ->getMock('\Magento\App\Area\FrontNameResolverFactory', array(), array(), '', false);
    }

    public function testGetCodeByFrontNameWhenAreaDoesNotContentFrontName()
    {
        $expected = 'expectedFrontName';
        $this->_model = new \Magento\App\AreaList($this->_frontNameResolverFactory,
            array('testArea' => array('frontNameResolver' => 'testValue')), $expected);

        $resolverMock = $this->getMock('\Magento\App\Area\FrontNameResolverInterface');
        $this->_frontNameResolverFactory->expects($this->any())->method('create')
            ->with('testValue')->will($this->returnValue($resolverMock));

        $actual = $this->_model->getCodeByFrontName('testFrontName');
        $this->assertEquals($actual, $expected);
    }

    public function testGetCodeByFrontNameReturnsAreaCode()
    {
        $expected = 'testArea';
        $this->_model = new \Magento\App\AreaList($this->_frontNameResolverFactory,
            array('testArea'=>array('frontName' => 'testFrontName')), $expected);

        $actual = $this->_model->getCodeByFrontName('testFrontName');
        $this->assertEquals($actual, $expected);
    }

    public function testGetFrontNameWhenAreaCodeAndFrontNameAreSet()
    {
        $expected = 'testFrontName';
        $this->_model = new \Magento\App\AreaList($this->_frontNameResolverFactory,
            array('testAreaCode' => array('frontName' => 'testFrontName')), $expected);

        $actual = $this->_model->getFrontName('testAreaCode');
        $this->assertEquals($actual, $expected);
    }

    public function testGetFrontNameWhenAreaCodeAndFrontNameArentSet()
    {
        $this->_model = new \Magento\App\AreaList($this->_frontNameResolverFactory, array(), '');

        $actual = $this->_model->getFrontName('testAreaCode');
        $this->assertNull($actual);
    }

    public function getFrontName()
    {
        $this->_model = new \Magento\App\AreaList($this->_frontNameResolverFactory, array(), '');

        $actual = $this->_model->getFrontName('testAreaCode');
        $this->assertNull($actual);
    }

    public function testGetCodes()
    {
        $this->_model = new \Magento\App\AreaList($this->_frontNameResolverFactory,
            array('area1' => 'value1', 'area2' => 'value2'), '');

        $expected = array(0 => 'area1', 1 => 'area2');
        $actual = $this->_model->getCodes();
        $this->assertEquals($actual, $expected);
    }
}
