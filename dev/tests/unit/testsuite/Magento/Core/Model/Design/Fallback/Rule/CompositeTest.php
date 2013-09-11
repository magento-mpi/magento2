<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Core_Model_Design_Fallback_Rule_CompositeTest extends PHPUnit_Framework_TestCase
{
    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Each item should implement the fallback rule interface
     */
    public function testConstructException()
    {
        new \Magento\Core\Model\Design\Fallback\Rule\Composite(array(new stdClass));
    }

    public function testGetPatternDirs()
    {
        $inputParams = array('param_one' => 'value_one', 'param_two' => 'value_two');

        $ruleOne = $this->getMockForAbstractClass('\Magento\Core\Model\Design\Fallback\Rule\RuleInterface');
        $ruleOne
            ->expects($this->once())
            ->method('getPatternDirs')
            ->with($inputParams)
            ->will($this->returnValue(array('rule_one/path/one', 'rule_one/path/two')))
        ;

        $ruleTwo = $this->getMockForAbstractClass('\Magento\Core\Model\Design\Fallback\Rule\RuleInterface');
        $ruleTwo
            ->expects($this->once())
            ->method('getPatternDirs')
            ->with($inputParams)
            ->will($this->returnValue(array('rule_two/path/one', 'rule_two/path/two')))
        ;

        $object = new \Magento\Core\Model\Design\Fallback\Rule\Composite(array($ruleOne, $ruleTwo));

        $expectedResult = array(
            'rule_one/path/one',
            'rule_one/path/two',
            'rule_two/path/one',
            'rule_two/path/two',
        );
        $this->assertEquals($expectedResult, $object->getPatternDirs($inputParams));
    }
}
