<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Core_Model_Design_Fallback_List_FileTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Core_Model_Design_Fallback_List_File
     */
    protected $_model;

    /**
     * @var array
     */
    protected $_defaultParams;

    public function setUp()
    {
        $filesystemHelper = new Magento_Test_Helper_FileSystem($this);
        $dirs = $filesystemHelper->createDirInstance(__DIR__, array(), array(
            Mage_Core_Model_Dir::THEMES => 'themes',
            Mage_Core_Model_Dir::MODULES => 'modules',
        ));
        $this->_model = new Mage_Core_Model_Design_Fallback_List_File($dirs);

        $parentTheme = $this->getMockForAbstractClass('Mage_Core_Model_ThemeInterface');
        $parentTheme->expects($this->any())->method('getThemePath')->will($this->returnValue('parent_theme_path'));

        $theme = $this->getMockForAbstractClass('Mage_Core_Model_ThemeInterface');
        $theme->expects($this->any())->method('getThemePath')->will($this->returnValue('current_theme_path'));
        $theme->expects($this->any())->method('getParentTheme')->will($this->returnValue($parentTheme));

        $this->_defaultParams = array(
            'area'      => 'area',
            'theme'     => $theme,
            'namespace' => 'namespace',
            'module'    => 'module',
        );
    }

    protected function tearDown()
    {
        $this->_model = null;
        $this->_defaultParams = array();
    }

    /**
     * @param array $overriddenParams
     * @param array $expectedResult
     * @dataProvider getPatternDirsDataProvider
     */
    public function testGetPatternDirs(array $overriddenParams, array $expectedResult)
    {
        $actualResult = $this->_model->getPatternDirs($overriddenParams + $this->_defaultParams);
        $this->assertEquals($expectedResult, $actualResult);
    }

    public function getPatternDirsDataProvider()
    {
        return array(
            'modular' => array(
                array(),
                array(
                    'themes/area/current_theme_path/namespace_module',
                    'themes/area/parent_theme_path/namespace_module',
                    'modules/namespace/module/view/area',
                ),
            ),
            'non-modular' => array(
                array('namespace' => null, 'module' => null),
                array(
                    'themes/area/current_theme_path',
                    'themes/area/parent_theme_path',
                ),
            ),
        );
    }

    /**
     * @param array $overriddenParams
     * @param $expectedErrorMessage
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
            'no namespace' => array(
                array('namespace' => null),
                "Parameters 'namespace' and 'module' should either be both set or unset",
            ),
            'no module' => array(
                array('module' => null),
                "Parameters 'namespace' and 'module' should either be both set or unset",
            ),
        );
    }
}
