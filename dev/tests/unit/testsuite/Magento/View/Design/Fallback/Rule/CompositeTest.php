<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\View\Design\Fallback\Rule;

/**
 * Composite Test
 *
 * @package Magento\View
 */
class CompositeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Each item should implement the fallback rule interface
     */
    public function testConstructException()
    {
        new Composite(array(new \stdClass()));
    }

    public function testGetPatternDirs()
    {
        $inputParams = array('param_one' => 'value_one', 'param_two' => 'value_two');

        $ruleOne = $this->getMockForAbstractClass('\Magento\View\Design\Fallback\Rule\RuleInterface');
        $ruleOne->expects(
            $this->once()
        )->method(
            'getPatternDirs'
        )->with(
            $inputParams
        )->will(
            $this->returnValue(array('rule_one/path/one', 'rule_one/path/two'))
        );

        $ruleTwo = $this->getMockForAbstractClass('\Magento\View\Design\Fallback\Rule\RuleInterface');
        $ruleTwo->expects(
            $this->once()
        )->method(
            'getPatternDirs'
        )->with(
            $inputParams
        )->will(
            $this->returnValue(array('rule_two/path/one', 'rule_two/path/two'))
        );

        $object = new Composite(array($ruleOne, $ruleTwo));

        $expectedResult = array('rule_one/path/one', 'rule_one/path/two', 'rule_two/path/one', 'rule_two/path/two');
        $this->assertEquals($expectedResult, $object->getPatternDirs($inputParams));
    }
}
