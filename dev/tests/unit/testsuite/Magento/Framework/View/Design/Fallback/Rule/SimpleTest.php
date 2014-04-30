<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\View\Design\Fallback\Rule;

/**
 * Simple Test
 *
 * @package Magento\Framework\View
 */
class SimpleTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Required parameter 'required_parameter' was not passed
     */
    public function testGetPatternDirsException()
    {
        $model = new Simple('<required_parameter> other text');
        $model->getPatternDirs(array());
    }

    /**
     * @dataProvider getPatternDirsDataProvider
     */
    public function testGetPatternDirs($pattern, $optionalParameter = null, $expectedResult = null)
    {
        $params = array('optional_parameter' => $optionalParameter, 'required_parameter' => 'required_parameter');
        $model = new Simple($pattern, array('optional_parameter'));

        $this->assertEquals($expectedResult, $model->getPatternDirs($params));
    }

    /**
     * @return array
     */
    public function getPatternDirsDataProvider()
    {
        $patternOptional = '<optional_parameter> <required_parameter> other text';
        $patternNoOptional = '<required_parameter> other text';

        return array(
            'no optional param passed' => array($patternOptional, null, array()),
            'no optional param in pattern' => array(
                $patternNoOptional,
                'optional_parameter',
                array('required_parameter other text')
            ),
            'optional params in pattern and passed' => array(
                $patternOptional,
                'optional_parameter',
                array('optional_parameter required_parameter other text')
            )
        );
    }
}
