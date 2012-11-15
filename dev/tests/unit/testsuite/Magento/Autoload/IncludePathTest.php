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
     * @var Magento_Autoload_IncludePath
     */
    protected $_loader;

    /**
     * @var string
     */
    protected $_includePath;

    protected function setUp()
    {
        $this->_includePath = get_include_path();
        $this->_loader = new Magento_Autoload_IncludePath;
        $this->_loader->addIncludePath(__DIR__);
    }

    protected function tearDown()
    {
        $this->_loader = null;
        set_include_path($this->_includePath);
    }

    /**
     * @param string $class
     * @param bool $expectedValue
     * @dataProvider autoloadDataProvider
     */
    public function testAutoload($class, $expectedValue)
    {
        $this->assertFalse(class_exists($class, false));
        $this->_loader->autoload($class);
        $this->assertEquals($expectedValue, class_exists($class, false));
    }

    /**
     * @return array
     */
    public function autoloadDataProvider()
    {
        return array(
            array('TestClass', true),
            array('\Ns\TestClass', true),
            array('Non_Existing_Class', false),
        );
    }

    public function testAddIncludePath()
    {
        $this->assertNotContains('before', get_include_path());
        $this->assertSame($this->_loader, $this->_loader->addIncludePath('before'));
        $this->assertStringStartsWith('before' . PATH_SEPARATOR, get_include_path());

        $this->assertNotContains('after', get_include_path());
        $this->_loader->addIncludePath(array('after'), true);
        $this->assertStringEndsWith(PATH_SEPARATOR . 'after', get_include_path());
    }
}
