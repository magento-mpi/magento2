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
        //self::$_model = new Magento_Config_Theme(glob(__DIR__ . '/_files/packages/*/*/theme.xml'));
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
        $config = new Magento_Config_Theme(array(
            sprintf('%s/_files/packages/%s/theme.xml', __DIR__, 'default/default')
        ));

        $this->assertFileExists($config->getSchemaFile());
    }

    /**
     * @param string $themePath
     * @param mixed $expected
     * @dataProvider getThemeTitleDataProvider
     */
    public function testGetThemeTitle($themePath, $expected)
    {
        $config = new Magento_Config_Theme(array(
            sprintf('%s/_files/packages/%s/theme.xml', __DIR__, $themePath)
        ));
        $this->assertSame($expected, $config->getThemeTitle());
    }

    /**
     * @return array
     */
    public function getThemeTitleDataProvider()
    {
        return array(
            array('default/default', 'Default'),
            array('default/test',    'Test'),
        );
    }

    /**
     * @param string $themePath
     * @param mixed $expected
     * @dataProvider getParentThemeDataProvider
     */
    public function testGetParentTheme($themePath, $expected)
    {
        $config = new Magento_Config_Theme(array(
            sprintf('%s/_files/packages/%s/theme.xml', __DIR__, $themePath)
        ));
        $this->assertSame($expected, $config->getParentTheme());
    }

    /**
     * @return array
     */
    public function getParentThemeDataProvider()
    {
        return array(
            array('default/default', null),
            array('default/test', array('default')),
            array('default/test2', array('test')),
            array('test/external_package_descendant', array('test2')),
        );
    }
}
