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
     * @expectedExceptionMessage Each element should implement Mage_Core_Model_Design_Fallback_Rule_RuleInterface
     */
    public function testConstructExceptionNotAnArray()
    {
        $patterns = array('not an interface');
        new Mage_Core_Model_Design_Fallback_Rule_Theme($patterns);
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

        $patternOne = '<theme_path> one';
        $patternTwo = '<theme_path> two';

        $mapOne = array(
            array(
                array('theme' => $theme, 'theme_path' => $theme->getThemePath()),
                array('package/theme one')
            ),
            array(
                array('theme' => $theme, 'theme_path' => $parentTheme->getThemePath()),
                array('parent_package/parent_theme one')
            )
        );

        $mapTwo = array(
            array(
                array('theme' => $theme, 'theme_path' => $theme->getThemePath()),
                array('package/theme two')
            ),
            array(
                array('theme' => $theme, 'theme_path' => $parentTheme->getThemePath()),
                array('parent_package/parent_theme two')
            )
        );

        $simpleRuleMockOne = $this->getMock(
            'Mage_Core_Model_Design_Fallback_Rule_Simple',
            array('getPatternDirs'),
            array($patternOne)
        );

        $simpleRuleMockTwo = $this->getMock(
            'Mage_Core_Model_Design_Fallback_Rule_Simple',
            array('getPatternDirs'),
            array($patternTwo)
        );

        $simpleRuleMockOne->expects($this->any())
            ->method('getPatternDirs')
            ->will($this->returnValueMap($mapOne));

        $simpleRuleMockTwo->expects($this->any())
            ->method('getPatternDirs')
            ->will($this->returnValueMap($mapTwo));

        $params = array('theme' => $theme);
        $model = new Mage_Core_Model_Design_Fallback_Rule_Theme(array($simpleRuleMockOne, $simpleRuleMockTwo));

        $expectedResult = array(
            'package/theme one',
            'package/theme two',
            'parent_package/parent_theme one',
            'parent_package/parent_theme two'
        );

        $this->assertEquals($expectedResult, $model->getPatternDirs($params));
    }
}
