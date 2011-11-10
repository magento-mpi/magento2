<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Framework
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Config_ThemeTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Config_Theme
     */
    protected static $_model = null;

    public static function setUpBeforeClass()
    {
        self::$_model = new Magento_Config_Theme(glob(__DIR__ . '/_files/packages/*/*/theme.xml'));
    }

    /**
     * @expectedException Exception
     */
    public function testConstructException()
    {
        new Magento_Config_Theme(glob(__DIR__ . '/_files/packages/*/theme.xml')); // no files will be found
    }

    public function testGetSchemaFile()
    {
        $this->assertFileExists(self::$_model->getSchemaFile());
    }

    /**
     * @param string $code
     * @param mixed $expected
     * @dataProvider getPackageTitleDataProvider
     */
    public function testGetPackageTitle($code, $expected)
    {
        $this->assertSame($expected, self::$_model->getPackageTitle($code));
    }

    /**
     * @return array
     */
    public function getPackageTitleDataProvider()
    {
        return array(
            array('default', 'Default'),
            array('test', 'Test'),
            array('invalid', false),
        );
    }

    /**
     * @param string $themeCode
     * @param string $packageCode
     * @param mixed $expected
     * @dataProvider getThemeTitleDataProvider
     */
    public function testGetThemeTitle($themeCode, $packageCode, $expected)
    {
        $this->assertSame($expected, self::$_model->getThemeTitle($themeCode, $packageCode));
    }

    /**
     * @return array
     */
    public function getThemeTitleDataProvider()
    {
        return array(
            array('default', 'default', 'Default'),
            array('test', 'default', 'Test'),
            array('invalid', 'invalid', false),
            array('default', 'invalid', false),
            array('invalid', 'default', false),
        );
    }

    /**
     * @dataProvider getCompatibleVersionsExceptionDataProvider
     * @expectedException Exception
     */
    public function testGetCompatibleVersionsException($package, $theme)
    {
        self::$_model->getCompatibleVersions($package, $theme);
    }

    public function getCompatibleVersionsExceptionDataProvider()
    {
        return array(
            array('test', 'unknown'),
            array('unknown', 'default'),
            array('unknown', 'unknown')
        );
    }

    /**
     * @dataProvider getCompatibleVersionsDataProvider
     */
    public function testGetCompatibleVersions($package, $theme, $versions)
    {
        $this->assertEquals($versions, self::$_model->getCompatibleVersions($package, $theme));
    }

    public function getCompatibleVersionsDataProvider()
    {
        return array(
            array('test', 'default', array('from' => '2.0.0.0-dev1', 'to' => '*')),
            array('default', 'test', array('from' => '2.0.0.0', 'to' => '*')),
        );
    }
}
