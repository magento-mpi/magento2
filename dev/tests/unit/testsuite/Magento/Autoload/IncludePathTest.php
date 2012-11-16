<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Autoload_IncludePathTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var string
     */
    protected static $_originalPath = '';

    public static function setUpBeforeClass()
    {
        self::$_originalPath = get_include_path();
    }

    protected function tearDown()
    {
        set_include_path(self::$_originalPath);
    }

    /**
     * @param string $class
     * @param bool $expectedValue
     * @dataProvider autoloadDataProvider
     */
    public function testGetFile($class, $expectedValue)
    {
        $this->assertFalse(Magento_Autoload_IncludePath::getFile($class));
        Magento_Autoload_IncludePath::addIncludePath(__DIR__ . '/_files');
        $this->assertEquals($expectedValue, Magento_Autoload_IncludePath::getFile($class));
    }

    /**
     * @return array
     */
    public function autoloadDataProvider()
    {
        return array(
            array('TestClass', realpath(__DIR__ . '/_files/TestClass.php')),
            array('\Ns\TestClass', realpath(__DIR__ . '/_files/Ns/TestClass.php')),
            array('Non_Existing_Class', false),
        );
    }

    public function testAddIncludePath()
    {
        $fixture = uniqid();
        $this->assertNotContains($fixture, get_include_path());
        Magento_Autoload_IncludePath::addIncludePath(array($fixture), true);
        $this->assertStringEndsWith(PATH_SEPARATOR . $fixture, get_include_path());
    }
}
