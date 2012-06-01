<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Core
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Core_Model_Design_FallbackTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Core_Model_Design_Fallback
     */
    protected $_model;

    public static function setUpBeforeClass()
    {
        Mage::app()->getConfig()->getOptions()->setDesignDir(
            dirname(__DIR__) . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR . 'design'
        );
    }

    protected function setUp()
    {
        $this->_model = new Mage_Core_Model_Design_Fallback;
    }

    /**
     * @param string $file
     * @param string $area
     * @param string $package
     * @param string $theme
     * @param string|null $module
     * @param string|null $expectedFilename
     *
     * @dataProvider getFileDataProvider
     */
    public function testGetFile($file, $area, $package, $theme, $module, $expectedFilename)
    {
        $expectedFilename = str_replace('/', DIRECTORY_SEPARATOR, $expectedFilename);
        $actualFilename = $this->_model->getFile($file, $area, $package, $theme, $module);
        if ($expectedFilename) {
            $this->assertStringMatchesFormat($expectedFilename, $actualFilename);
            $this->assertFileExists($actualFilename);
        } else {
            $this->assertFileNotExists($actualFilename);
        }
    }

    public function getFileDataProvider()
    {
        return array(
            'no default theme inheritance' => array(
                'fixture_template.phtml', 'frontend', 'package', 'standalone_theme', null, null
            ),
            'same package & parent theme' => array(
                'fixture_template_two.phtml', 'frontend', 'package', 'custom_theme_descendant', null,
                "%s/frontend/package/custom_theme/fixture_template_two.phtml",
            ),
            'same package & grandparent theme' => array(
                'fixture_template.phtml', 'frontend', 'package', 'custom_theme_descendant', null,
                "%s/frontend/package/default/fixture_template.phtml",
            ),
            'parent package & parent theme' => array(
                'fixture_template_two.phtml', 'frontend', 'test', 'external_package_descendant', null,
                "%s/frontend/package/custom_theme/fixture_template_two.phtml",
            ),
            'parent package & grandparent theme' => array(
                'fixture_template.phtml', 'frontend', 'test', 'external_package_descendant', null,
                "%s/frontend/package/default/fixture_template.phtml",
            ),
            'module file inherited by scheme' => array(
                'theme_template.phtml', 'frontend', 'test', 'test_theme', 'Mage_Catalog',
                "%s/frontend/test/default/Mage_Catalog/theme_template.phtml",
            )
        );
    }

    /**
     * @param string $file
     * @param string $area
     * @param string $package
     * @param string $theme
     * @param string $locale
     * @param string|null $expectedFilename
     *
     * @dataProvider getLocaleFileDataProvider
     */
    public function testLocaleFileFallback($file, $area, $package, $theme, $locale, $expectedFilename)
    {
        $expectedFilename = str_replace('/', DIRECTORY_SEPARATOR, $expectedFilename);
        $actualFilename = $this->_model->getLocaleFile($file, $area, $package, $theme, $locale);
        if ($expectedFilename) {
            $this->assertStringMatchesFormat($expectedFilename, $actualFilename);
            $this->assertFileExists($actualFilename);
        } else {
            $this->assertFileNotExists($actualFilename);
        }
    }

    public function getLocaleFileDataProvider()
    {
        return array(
            'no default theme inheritance' => array(
                'fixture_translate.csv', 'frontend', 'package', 'standalone_theme', 'en_US', null
            ),
            'parent theme' => array(
                'fixture_translate_two.csv', 'frontend', 'package', 'theme' => 'custom_theme_descendant', 'en_US',
                "%s/frontend/package/custom_theme/locale/en_US/fixture_translate_two.csv",
            ),
            'grandparent theme' => array(
                'fixture_translate.csv', 'frontend', 'package', 'custom_theme_descendant', 'en_US',
                "%s/frontend/package/default/locale/en_US/fixture_translate.csv",
            ),
        );
    }

    /**
     * Test for the skin files fallback
     *
     * @param string $file
     * @param string $area
     * @param string $package
     * @param string $theme
     * @param string|null $skin
     * @param string|null $locale
     * @param string|null $module
     * @param string|null $expectedFilename
     */
    protected function _testGetSkinFile($file, $area, $package, $theme, $skin, $locale, $module, $expectedFilename)
    {
        $expectedFilename = str_replace('/', DIRECTORY_SEPARATOR, $expectedFilename);
        $actualFilename = $this->_model->getSkinFile($file, $area, $package, $theme, $skin, $locale, $module);
        if ($expectedFilename) {
            $this->assertStringMatchesFormat($expectedFilename, $actualFilename);
            $this->assertFileExists($actualFilename);
        } else {
            $this->assertFileNotExists($actualFilename);
        }
    }

    /**
     * Test for the skin files fallback according to the themes inheritance
     *
     * @param string $file
     * @param string $area
     * @param string $package
     * @param string $theme
     * @param string $skin
     * @param string $locale
     * @param string|null $expectedFilename
     *
     * @dataProvider getSkinFileThemeDataProvider
     */
    public function testGetSkinFileTheme($file, $area, $package, $theme, $skin, $locale, $expectedFilename)
    {
        $this->_testGetSkinFile($file, $area, $package, $theme, $skin, $locale, null, $expectedFilename);
    }

    public function getSkinFileThemeDataProvider()
    {
        return array(
            'no default theme inheritance' => array(
                'fixture_script_two.js', 'frontend', 'package', 'standalone_theme', 'theme_nested_skin', 'en_US',
                null,
            ),
            'same theme & default skin' => array(
                'fixture_script_two.js', 'frontend', 'package', 'custom_theme', 'theme_nested_skin', 'en_US',
                "%s/frontend/package/custom_theme/skin/default/fixture_script_two.js",
            ),
            'parent theme & same skin' => array(
                'fixture_script.js', 'frontend', 'package', 'custom_theme_descendant', 'theme_nested_skin', 'en_US',
                "%s/frontend/package/custom_theme/skin/theme_nested_skin/fixture_script.js",
            ),
            'parent theme & default skin' => array(
                'fixture_script_two.js', 'frontend', 'package', 'custom_theme_descendant', 'theme_nested_skin', 'en_US',
                "%s/frontend/package/custom_theme/skin/default/fixture_script_two.js",
            ),
            'grandparent theme & same skin' => array(
                'fixture_script_three.js', 'frontend', 'package', 'custom_theme_descendant', 'theme_nested_skin',
                'en_US',  "%s/frontend/package/default/skin/theme_nested_skin/fixture_script_three.js",
            ),
            'grandparent theme & default skin' => array(
                'fixture_script_four.js', 'frontend', 'package', 'custom_theme_descendant', 'theme_nested_skin',
                'en_US', "%s/frontend/package/default/skin/default/fixture_script_four.js",
            ),
            'parent package & same theme & same skin' => array(
                'fixture_script.js', 'frontend', 'test', 'external_package_descendant', 'theme_nested_skin', 'en_US',
                "%s/frontend/package/custom_theme/skin/theme_nested_skin/fixture_script.js",
            ),
            'parent package & same theme & default skin' => array(
                'fixture_script_two.js', 'frontend', 'test', 'external_package_descendant', 'theme_nested_skin',
                'en_US', "%s/frontend/package/custom_theme/skin/default/fixture_script_two.js",
            ),
        );
    }

    /**
     * Test for the skin files localization
     *
     * @param string $file
     * @param string $area
     * @param string $package
     * @param string $theme
     * @param string $skin
     * @param string $locale
     * @param string|null $module
     * @param string|null $expectedFilename
     *
     * @dataProvider getSkinFileL10nDataProvider
     */
    public function testGetSkinFileL10n($file, $area, $package, $theme, $skin, $locale, $module, $expectedFilename)
    {
        $this->_testGetSkinFile($file, $area, $package, $theme, $skin, $locale, $module, $expectedFilename);
    }

    public function getSkinFileL10nDataProvider()
    {
        return array(
            'general skin file' => array(
                'fixture_script.js', 'frontend', 'package', 'custom_theme', 'theme_nested_skin', 'en_US', null,
                "%s/frontend/package/custom_theme/skin/theme_nested_skin/fixture_script.js"
            ),
            'localized skin file' => array(
                'fixture_script.js', 'frontend', 'package', 'custom_theme', 'theme_nested_skin', 'ru_RU', null,
                "%s/frontend/package/custom_theme/skin/theme_nested_skin/locale/ru_RU/fixture_script.js",
            ),
            'general modular skin file' => array(
                'fixture_script.js', 'frontend', 'package', 'custom_theme', 'theme_nested_skin', 'en_US',
                'Fixture_Module',
                "%s/frontend/package/custom_theme/skin/theme_nested_skin/Fixture_Module/fixture_script.js",
            ),
            'localized modular skin file' => array(
                'fixture_script.js', 'frontend', 'package', 'custom_theme', 'theme_nested_skin', 'ru_RU',
                'Fixture_Module',
                "%s/frontend/package/custom_theme/skin/theme_nested_skin/locale/ru_RU/Fixture_Module/fixture_script.js",
            ),
        );
    }

    /**
     * Test for the skin files fallback to the JavaScript libraries
     *
     * @param string $file
     * @param string $area
     * @param string $package
     * @param string $theme
     * @param string $skin
     * @param string|null $expectedFilename
     *
     * @dataProvider getSkinFileJsLibDataProvider
     */
    public function testGetSkinFileJsLib($file, $area, $package, $theme, $skin, $expectedFilename)
    {
        $this->_testGetSkinFile($file, $area, $package, $theme, $skin, 'en_US', null, $expectedFilename);
    }

    public function getSkinFileJsLibDataProvider()
    {
        return array(
            'lib file in theme' => array(
                'mage/jquery-no-conflict.js', 'frontend', 'package', 'custom_theme', 'theme_nested_skin',
                "%s/frontend/package/custom_theme/skin/theme_nested_skin/mage/jquery-no-conflict.js",
            ),
            'lib file in js lib' => array(
                'mage/jquery-no-conflict.js', 'frontend', 'package', 'custom_theme', 'default',
                '%s/pub/js/mage/jquery-no-conflict.js',
            ),
        );
    }

    /**
     * @param string $area
     * @param string $package
     * @param string $theme
     * @param array $expected
     *
     * @dataProvider getInheritedThemeDataProvider
     */
    public function testGetInheritedTheme($area, $package, $theme, $expected)
    {
        $actual = $this->_model->getInheritedTheme($area, $package, $theme);
        $this->assertEquals($expected, $actual);
    }

    /**
     * @return array
     */
    public function getInheritedThemeDataProvider()
    {
        return array(
            'standalone_theme' => array('frontend', 'package', 'standalone_theme', false),
            'descendant_theme' => array(
                'frontend', 'package', 'custom_theme_descendant', array('package', 'custom_theme')
            ),
            'external_package_descendant_theme' => array(
                'frontend', 'test', 'external_package_descendant', array('package', 'custom_theme')
            )
        );
    }
}
