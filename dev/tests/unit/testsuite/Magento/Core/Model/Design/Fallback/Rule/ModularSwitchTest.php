<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Core_Model_Design_Fallback_Rule_ModularSwitchTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Core_Model_Design_Fallback_Rule_ModularSwitch
     */
    protected $_object;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_ruleNonModular;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_ruleModular;

    protected function setUp()
    {
        $this->_ruleNonModular = $this->getMockForAbstractClass(
            'Magento_Core_Model_Design_Fallback_Rule_RuleInterface'
        );
        $this->_ruleModular = $this->getMockForAbstractClass(
            'Magento_Core_Model_Design_Fallback_Rule_RuleInterface'
        );
        $this->_object = new Magento_Core_Model_Design_Fallback_Rule_ModularSwitch(
            $this->_ruleNonModular, $this->_ruleModular
        );
    }

    protected function tearDown()
    {
        $this->_object = null;
        $this->_ruleNonModular = null;
        $this->_ruleModular = null;
    }

    public function testGetPatternDirsNonModular()
    {
        $inputParams = array('param_one' => 'value_one', 'param_two' => 'value_two');
        $expectedResult = new stdClass();
        $this->_ruleNonModular
            ->expects($this->once())
            ->method('getPatternDirs')
            ->with($inputParams)
            ->will($this->returnValue($expectedResult))
        ;
        $this->_ruleModular
            ->expects($this->never())
            ->method('getPatternDirs')
        ;
        $this->assertSame($expectedResult, $this->_object->getPatternDirs($inputParams));
    }

    public function testGetPatternDirsModular()
    {
        $inputParams = array('param' => 'value', 'namespace' => 'Magento', 'module' => 'Core');
        $expectedResult = new stdClass();
        $this->_ruleNonModular
            ->expects($this->never())
            ->method('getPatternDirs')
        ;
        $this->_ruleModular
            ->expects($this->once())
            ->method('getPatternDirs')
            ->with($inputParams)
            ->will($this->returnValue($expectedResult))
        ;
        $this->assertSame($expectedResult, $this->_object->getPatternDirs($inputParams));
    }

    /**
     * @param array $inputParams
     * @dataProvider getPatternDirsExceptionDataProvider
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Parameters 'namespace' and 'module' should either be both set or unset
     */
    public function testGetPatternDirsException(array $inputParams)
    {
        $this->_object->getPatternDirs($inputParams);
    }

    public function getPatternDirsExceptionDataProvider()
    {
        return array(
            'no namespace'  => array(array('module' => 'Core')),
            'no module'     => array(array('namespace' => 'Magento')),
        );
    }
}
