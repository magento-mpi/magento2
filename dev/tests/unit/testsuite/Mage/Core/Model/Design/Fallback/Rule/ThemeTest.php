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

class Mage_Core_Model_Design_Fallback_Rule_ThemeTest extends PHPUnit_Framework_TestCase
{
    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Each pattern in list must be an array
     */
    public function testConstructExceptionNotAnArray()
    {
        $patterns = array('not an array');
        $model = new Mage_Core_Model_Design_Fallback_Rule_Theme($patterns);
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Pattern must contain '<theme_path>' node
     */
    public function testConstructExceptionNoThemePath()
    {
        $patterns = array(array('no theme path'));
        $model = new Mage_Core_Model_Design_Fallback_Rule_Theme($patterns);
    }

    public function testGetPatternsDirs()
    {
        $parentThemePath = 'parent_package/parent_theme';
        $parentTheme = $this->getMock('Mage_Core_Model_Theme', array('getThemePath'), array(), '', false);
        $parentTheme->expects($this->any())
            ->method('getThemePath')
            ->will($this->returnValue($parentThemePath));

        $themePath = 'package/theme';
        $theme = $this->getMock('Mage_Core_Model_Theme', array('getThemePath', 'getParentTheme'), array(), '', false);
        $theme->expects($this->any())
            ->method('getThemePath')
            ->will($this->returnValue($themePath));

        $theme->expects($this->any())
            ->method('getParentTheme')
            ->will($this->returnValue($parentTheme));

        $patternOne = '<theme_path> <other_one> one';
        $patternTwo = '<theme_path> <other_two> two';
        $params = array('other_one' => 'oo', 'other_two' => 'ot', 'theme' => $theme);
        $model = new Mage_Core_Model_Design_Fallback_Rule_Theme(array(array($patternOne), array($patternTwo)));

        $expectedResult = array(
            array(
                'dir' => 'package/theme oo one',
                'pattern' => $patternOne
            ),
            array(
                'dir' => 'package/theme ot two',
                'pattern' => $patternTwo
            ),
            array(
                'dir' => 'parent_package/parent_theme oo one',
                'pattern' => $patternOne
            ),
            array(
                'dir' => 'parent_package/parent_theme ot two',
                'pattern' => $patternTwo
            )
        );

        $this->assertEquals($expectedResult, $model->getPatternDirs($params));
    }
}
