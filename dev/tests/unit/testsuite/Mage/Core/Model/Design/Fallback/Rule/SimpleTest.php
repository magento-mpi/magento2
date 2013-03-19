<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Core
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Core_Model_Design_Fallback_Rule_SimpleTest extends PHPUnit_Framework_TestCase
{
    /**
     * @expectedException InvalidArgumentException
     */
    public function testGetPatternsDirsException()
    {
        $model = new Mage_Core_Model_Design_Fallback_Rule_Simple('<other_param> other text');
        $model->getPatternDirs(array());
    }

    /**
     * @dataProvider getPatternsDirsDataProvider
     */
    public function testGetPatternsDirs($pattern, $param = null, $expectedResult = null)
    {
        $params = array(
            'param' => $param,
            'other_param' => 'other param',
        );
        $model = new Mage_Core_Model_Design_Fallback_Rule_Simple($pattern, array('param'));

        $this->assertEquals(
            $expectedResult,
            $model->getPatternDirs($params)
        );
    }

    public function getPatternsDirsDataProvider()
    {
        $patternOptional = '<param> <other_param> other text';
        $patternNoOptional = '<other_param> other text';

        return array(
            'no optional param' => array(
                $patternOptional,
                null,
                array()
            ),
            'no modules in pattern' => array(
                $patternNoOptional,
                'Module',
                array('other param other text')
            ),
            'modules' => array(
                $patternOptional,
                'Module',
                array('Module other param other text')
            ),
        );
    }
}
