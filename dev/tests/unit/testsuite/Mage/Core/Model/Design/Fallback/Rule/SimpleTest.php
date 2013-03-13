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
        $model->getPatternDirs('', array(), array());
    }

    /**
     * @dataProvider getPatternsDirsDataProvider
     */
    public function testGetPatternsDirs($pattern, $module = null, $expectedResult = null)
    {
        $params = array(
            'module' => $module,
            'other_param' => 'other param'
        );
        $model = new Mage_Core_Model_Design_Fallback_Rule_Simple($pattern);

        $this->assertEquals(
            $model->getPatternDirs('', $params, array()),
            $expectedResult
        );
    }

    public function getPatternsDirsDataProvider()
    {
        $patternModules = '<module> <other_param> other text';
        $patternNoModules = '<other_param> other text';

        return array(
            'no modules in param' => array(
                $patternModules,
                null,
                array()
            ),
            'no modules in pattern' => array(
                $patternNoModules,
                'Module',
                array(array('dir' => 'other param other text', 'pattern' => $patternNoModules))
            ),
            'modules' => array(
                $patternModules,
                'Module',
                array(array('dir' => 'Module other param other text', 'pattern' => $patternModules))
            ),
        );
    }
}
