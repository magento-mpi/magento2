<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Core_Model_Design_Fallback_FactoryTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Core_Model_Design_Fallback_Factory
     */
    protected $_model;

    /**
     * @var array
     */
    protected $_defaultParams;

    public function setUp()
    {
        $dirs = new Magento_Core_Model_Dir(__DIR__, array(), array(
            Magento_Core_Model_Dir::THEMES => 'themes',
            Magento_Core_Model_Dir::MODULES => 'modules',
            Magento_Core_Model_Dir::PUB_LIB => 'pub_lib',
        ));
        $this->_model = new Magento_Core_Model_Design_Fallback_Factory($dirs);

        $parentTheme = $this->getMockForAbstractClass('Magento_Core_Model_ThemeInterface');
        $parentTheme->expects($this->any())->method('getThemePath')->will($this->returnValue('parent_theme_path'));

        $theme = $this->getMockForAbstractClass('Magento_Core_Model_ThemeInterface');
        $theme->expects($this->any())->method('getThemePath')->will($this->returnValue('current_theme_path'));
        $theme->expects($this->any())->method('getParentTheme')->will($this->returnValue($parentTheme));

        $this->_defaultParams = array(
            'area'      => 'area',
            'theme'     => $theme,
            'namespace' => 'namespace',
            'module'    => 'module',
            'locale'    => 'en_US',
        );
    }

    protected function tearDown()
    {
        $this->_model = null;
        $this->_defaultParams = array();
    }

    public function testCreateLocaleFileRule()
    {
        $actualResult = $this->_model->createLocaleFileRule();
        $this->assertInstanceOf('Magento_Core_Model_Design_Fallback_Rule_RuleInterface', $actualResult);
        $this->assertNotSame($actualResult, $this->_model->createLocaleFileRule());
    }

    public function testCreateLocaleFileRuleGetPatternDirs()
    {
        $expectedResult = array(
            'themes/area/current_theme_path/i18n/en_US',
            'themes/area/parent_theme_path/i18n/en_US',
        );
        $this->assertSame(
            $expectedResult, $this->_model->createLocaleFileRule()->getPatternDirs($this->_defaultParams)
        );
    }

    /**
     * @param array $overriddenParams
     * @param string $expectedErrorMessage
     * @dataProvider createLocaleFileRuleGetPatternDirsExceptionDataProvider
     */
    public function testCreateLocaleFileRuleGetPatternDirsException(array $overriddenParams, $expectedErrorMessage)
    {
        $this->setExpectedException('InvalidArgumentException', $expectedErrorMessage);
        $this->_model->createLocaleFileRule()->getPatternDirs($overriddenParams + $this->_defaultParams);
    }

    public function createLocaleFileRuleGetPatternDirsExceptionDataProvider()
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

    public function testCreateFileRule()
    {
        $actualResult = $this->_model->createFileRule();
        $this->assertInstanceOf('Magento_Core_Model_Design_Fallback_Rule_RuleInterface', $actualResult);
        $this->assertNotSame($actualResult, $this->_model->createFileRule());
    }

    /**
     * @param array $overriddenParams
     * @param array $expectedResult
     * @dataProvider createFileRuleGetPatternDirsDataProvider
     */
    public function testCreateFileRuleGetPatternDirs(array $overriddenParams, array $expectedResult)
    {
        $actualResult = $this->_model->createFileRule()->getPatternDirs($overriddenParams + $this->_defaultParams);
        $this->assertEquals($expectedResult, $actualResult);
    }

    public function createFileRuleGetPatternDirsDataProvider()
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
     * @dataProvider createRuleGetPatternDirsExceptionDataProvider
     */
    public function testCreateFileRuleGetPatternDirsException(array $overriddenParams, $expectedErrorMessage)
    {
        $this->setExpectedException('InvalidArgumentException', $expectedErrorMessage);
        $this->_model->createFileRule()->getPatternDirs($overriddenParams + $this->_defaultParams);
    }

    public function testCreateViewFileRule()
    {
        $actualResult = $this->_model->createViewFileRule();
        $this->assertInstanceOf('Magento_Core_Model_Design_Fallback_Rule_RuleInterface', $actualResult);
        $this->assertNotSame($actualResult, $this->_model->createViewFileRule());
    }

    /**
     * @param array $overriddenParams
     * @param array $expectedResult
     * @dataProvider createViewFileRuleGetPatternDirsDataProvider
     */
    public function testCreateViewFileRuleGetPatternDirs(array $overriddenParams, array $expectedResult)
    {
        $actualResult = $this->_model->createViewFileRule()->getPatternDirs($overriddenParams + $this->_defaultParams);
        $this->assertEquals($expectedResult, $actualResult);
    }

    public function createViewFileRuleGetPatternDirsDataProvider()
    {
        return array(
            'modular localized' => array(
                array(),
                array(
                    'themes/area/current_theme_path/i18n/en_US/namespace_module',
                    'themes/area/current_theme_path/namespace_module',
                    'themes/area/parent_theme_path/i18n/en_US/namespace_module',
                    'themes/area/parent_theme_path/namespace_module',
                    'modules/namespace/module/view/area/i18n/en_US',
                    'modules/namespace/module/view/area',
                ),
            ),
            'modular non-localized' => array(
                array('locale' => null),
                array(
                    'themes/area/current_theme_path/namespace_module',
                    'themes/area/parent_theme_path/namespace_module',
                    'modules/namespace/module/view/area',
                ),
            ),
            'non-modular localized' => array(
                array('module' => null, 'namespace' => null),
                array(
                    'themes/area/current_theme_path/i18n/en_US',
                    'themes/area/current_theme_path',
                    'themes/area/parent_theme_path/i18n/en_US',
                    'themes/area/parent_theme_path',
                    'pub_lib',
                ),
            ),
            'non-modular non-localized' => array(
                array('module' => null, 'namespace' => null, 'locale' => null),
                array(
                    'themes/area/current_theme_path',
                    'themes/area/parent_theme_path',
                    'pub_lib',
                ),
            ),
        );
    }

    /**
     * @param array $overriddenParams
     * @param $expectedErrorMessage
     * @dataProvider createRuleGetPatternDirsExceptionDataProvider
     */
    public function testCreateViewFileRuleGetPatternDirsException(array $overriddenParams, $expectedErrorMessage)
    {
        $this->setExpectedException('InvalidArgumentException', $expectedErrorMessage);
        $this->_model->createViewFileRule()->getPatternDirs($overriddenParams + $this->_defaultParams);
    }

    public function createRuleGetPatternDirsExceptionDataProvider()
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
