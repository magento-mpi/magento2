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
     * @param mixed $constructorArgument
     * @dataProvider constructorExceptionDataProvider
     * @expectedException InvalidArgumentException
     */
    public function testConstructException($constructorArgument)
    {
        new Magento_Config_Theme($constructorArgument);
    }

    public function constructorExceptionDataProvider()
    {
        return array(
            'empty files list' => array(array()),
            'wrong data type'  => array(0.123),
        );
    }

    /**
     * Test that new object based on the exported data behaves identically to the one the data have been exported from
     */
    public function testConstructorExportData()
    {
        $model = new Magento_Config_Theme(self::$_model->exportData());
        $this->assertEquals(self::$_model->exportData(), $model->exportData());
        $this->assertEquals(self::$_model->getPackages(), $model->getPackages());
        foreach (self::$_model->getPackages() as $package) {
            $this->assertEquals(self::$_model->getPackageTitle($package), $model->getPackageTitle($package));
            $this->assertEquals(self::$_model->getThemes($package), $model->getThemes($package));
            foreach (self::$_model->getThemes($package) as $theme) {
                $this->assertEquals(
                    self::$_model->getThemeTitle($package, $theme),
                    $model->getThemeTitle($package, $theme)
                );
                $this->assertEquals(
                    self::$_model->getCompatibleVersions($package, $theme),
                    $model->getCompatibleVersions($package, $theme)
                );
                $this->assertEquals(
                    self::$_model->getParentTheme($package, $theme),
                    $model->getParentTheme($package, $theme)
                );
            }
        }
    }

    public function testGetSchemaFile()
    {
        $this->assertFileExists(self::$_model->getSchemaFile());
    }

    public function testGetPackages()
    {
        $this->assertEquals(array('default', 'test'), self::$_model->getPackages());
    }

    /**
     * @param string $package
     * @param array $expectedThemes
     * @dataProvider getThemesDataProvider
     */
    public function testGetThemes($package, array $expectedThemes)
    {
        $this->assertEquals($expectedThemes, self::$_model->getThemes($package));
    }

    public function getThemesDataProvider()
    {
        return array(
            array('default', array('default', 'test', 'test2')),
            array('test',    array('default')),
        );
    }

    /**
     * @param string $package
     * @param mixed $expected
     * @dataProvider getPackageTitleDataProvider
     */
    public function testGetPackageTitle($package, $expected)
    {
        $this->assertSame($expected, self::$_model->getPackageTitle($package));
    }

    /**
     * @return array
     */
    public function getPackageTitleDataProvider()
    {
        return array(
            array('default', 'Default'),
            array('test',    'Test'),
        );
    }

    /**
     * @expectedException Magento_Exception
     */
    public function testGetPackageTitleException()
    {
        self::$_model->getPackageTitle('invalid');
    }

    /**
     * @param string $package
     * @param string $theme
     * @param mixed $expected
     * @dataProvider getThemeTitleDataProvider
     */
    public function testGetThemeTitle($package, $theme, $expected)
    {
        $this->assertSame($expected, self::$_model->getThemeTitle($package, $theme));
    }

    /**
     * @return array
     */
    public function getThemeTitleDataProvider()
    {
        return array(
            array('default', 'default', 'Default'),
            array('default', 'test',    'Test'),
        );
    }

    /**
     * @param string $package
     * @param string $theme
     * @param mixed $expected
     * @dataProvider getParentThemeDataProvider
     */
    public function testGetParentTheme($package, $theme, $expected)
    {
        $this->assertSame($expected, self::$_model->getParentTheme($package, $theme));
    }

    /**
     * @return array
     */
    public function getParentThemeDataProvider()
    {
        return array(
            array('default', 'default', null),
            array('default', 'test',    'default'),
            array('default', 'test2',   'test'),
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

    /**
     * @param string $getter
     * @param string $package
     * @param string $theme
     * @dataProvider ensureThemeExistsExceptionDataProvider
     * @expectedException Magento_Exception
     */
    public function testEnsureThemeExistsException($getter, $package, $theme)
    {
        self::$_model->$getter($package, $theme);
    }

    /**
     * @return array
     */
    public function ensureThemeExistsExceptionDataProvider()
    {
        $result = array();
        foreach (array('getThemeTitle', 'getParentTheme', 'getCompatibleVersions') as $getter) {
            $result[] = array($getter, 'invalid', 'invalid');
            $result[] = array($getter, 'default', 'invalid');
            $result[] = array($getter, 'invalid', 'default');
        }
        return $result;
    }
}
