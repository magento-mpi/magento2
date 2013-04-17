<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Core_Model_Design_Fallback_List_LocaleTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Core_Model_Design_Fallback_List_Locale
     */
    protected $_model;

    /**
     * @var array
     */
    protected $_defaultParams;

    public function setUp()
    {
        $filesystemHelper = new Magento_Test_Helper_FileSystem($this);
        $dirs = $filesystemHelper->createDirInstance(__DIR__, array(), array(Mage_Core_Model_Dir::THEMES => 'themes'));
        $this->_model = new Mage_Core_Model_Design_Fallback_List_Locale($dirs);

        $parentTheme = $this->getMockForAbstractClass('Mage_Core_Model_ThemeInterface');
        $parentTheme->expects($this->any())->method('getThemePath')->will($this->returnValue('parent_theme_path'));

        $theme = $this->getMockForAbstractClass('Mage_Core_Model_ThemeInterface');
        $theme->expects($this->any())->method('getThemePath')->will($this->returnValue('current_theme_path'));
        $theme->expects($this->any())->method('getParentTheme')->will($this->returnValue($parentTheme));

        $this->_defaultParams = array(
            'area' => 'area',
            'theme' => $theme,
            'locale' => 'en_US',
        );
    }

    protected function tearDown()
    {
        $this->_model = null;
        $this->_defaultParams = array();
    }

    public function testGetPatternDirs()
    {
        $expectedResult = array(
            'themes/area/current_theme_path/locale/en_US',
            'themes/area/parent_theme_path/locale/en_US',
        );
        $this->assertSame($expectedResult, $this->_model->getPatternDirs($this->_defaultParams));
    }

    /**
     * @param array $overriddenParams
     * @param string $expectedErrorMessage
     * @dataProvider getPatternDirsExceptionDataProvider
     */
    public function testGetPatternDirsException(array $overriddenParams, $expectedErrorMessage)
    {
        $this->setExpectedException('InvalidArgumentException', $expectedErrorMessage);
        $this->_model->getPatternDirs($overriddenParams + $this->_defaultParams);
    }

    public function getPatternDirsExceptionDataProvider()
    {
        return array(
            'no theme' => array(
                array('theme' => null),
                'Parameter "theme" should be specified and should implement the theme interface',
            ),
            'no area' => array(
                array('area' => null),
                "Required parameter 'area' was not passed",
            ),
            'no locale' => array(
                array('locale' => null),
                "Required parameter 'locale' was not passed",
            ),
        );
    }
}
