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
     */
    public function testConstructException()
    {
        $patterns = array('pattern');
        $model = new Mage_Core_Model_Design_Fallback_Rule_Theme($patterns);
    }

    public function testGetPatternsDirs()
    {
        $themePath = 'package/theme';
        $theme = $this->getMock('Mage_Core_Model_Theme', array('getThemePath'), array(), '', false);
        $theme->expects($this->any())
            ->method('getThemePath')
            ->will($this->returnValue($themePath));


        $parentThemePath = 'parent_package/parent_theme';
        $parentTheme = $this->getMock('Mage_Core_Model_Theme', array('getThemePath'), array(), '', false);
        $parentTheme->expects($this->any())
            ->method('getThemePath')
            ->will($this->returnValue($parentThemePath));

        $themeList = array($theme, $parentTheme);

        $pattern1 = '<theme_path> <other_one> one';
        $pattern2 = '<theme_path> <other_two> two';
        $params = array('other_one' => 'oo', 'other_two' => 'ot');
        $model = new Mage_Core_Model_Design_Fallback_Rule_Theme(array($pattern1, $pattern2));

        $expectedResult = array(
            array(
                'dir' => 'package/theme oo one',
                'pattern' => $pattern1
            ),
            array(
                'dir' => 'package/theme ot two',
                'pattern' => $pattern2
            ),
            array(
                'dir' => 'parent_package/parent_theme oo one',
                'pattern' => $pattern1
            ),
            array(
                'dir' => 'parent_package/parent_theme ot two',
                'pattern' => $pattern2
            )
        );

        $this->assertEquals($model->getPatternDirs('', $params, $themeList), $expectedResult);
    }
}
