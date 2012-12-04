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
     * @param bool string|$expectedValue
     * @dataProvider getFileDataProvider
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
    public function getFileDataProvider()
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
        $this->assertStringStartsWith($fixture . PATH_SEPARATOR, get_include_path());
    }

    /**
     * @param string $class
     * @param string|bool $expectedValue
     * @dataProvider getFileDataProvider
     */
    public function testLoad($class, $expectedValue)
    {
        Magento_Autoload_IncludePath::addIncludePath(__DIR__ . '/_files');
        $this->assertFalse(class_exists($class, false));
        Magento_Autoload_IncludePath::load($class);
        if ($expectedValue) {
            $this->assertTrue(class_exists($class, false));
        } else {
            $this->assertFalse(class_exists($class, false));
        }
    }
}
