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
        self::$_model = new Magento_Config_Theme(glob(__DIR__ . '/_files/area/*/theme.xml'));
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testConstructException()
    {
        new Magento_Config_Theme(array());
    }

    public function testGetSchemaFile()
    {
        $this->assertFileExists(self::$_model->getSchemaFile());
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
            array('default', 'default_default', 'Default'),
            array('default', 'default_test',    'Test'),
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
            array('default', 'default_default', null),
            array('default', 'default_test', array('default_default')),
            array('default', 'default_test2', array('default_test')),
            array('test', 'test_external_package_descendant', array('default_test2')),
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
            array('test', 'test_default', array('from' => '2.0.0.0-dev1', 'to' => '*')),
            array('default', 'default_test', array('from' => '2.0.0.0', 'to' => '*')),
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
