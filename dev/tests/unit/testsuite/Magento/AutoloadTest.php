<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Magento_Autoload test case.
 */
class Magento_AutoloadTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Autoload
     */
    protected $_loader;
    protected $_includePath;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        parent::setUp();
        $this->_includePath = get_include_path();
        $this->_loader = Magento_Autoload::getInstance();
        $this->_loader->addIncludePath(__DIR__ . DIRECTORY_SEPARATOR . 'Autoload');
    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown()
    {
        $this->_loader = null;
        set_include_path($this->_includePath);
        parent::tearDown();
    }

    public function testGetInstance()
    {
        $this->assertSame($this->_loader, Magento_Autoload::getInstance());
    }

    public function testClassExists()
    {
        $this->assertTrue($this->_loader->classExists('PHPUnit_Framework_TestCase'));
        $this->assertTrue($this->_loader->classExists('TestClassExists'));
        $this->assertFalse($this->_loader->classExists('class_not_exists'));
    }

    public function testAutoload()
    {
        $this->assertFalse(class_exists('TestClass', false));
        $this->_loader->autoload('TestClass');
        $this->assertTrue(class_exists('TestClass', false));

        $this->assertFalse(class_exists('\Ns\TestClass', false));
        $this->_loader->autoload('\Ns\TestClass');
        $this->assertTrue(class_exists('\Ns\TestClass', false));
    }

    public function testAddIncludePath()
    {
        $this->assertNotContains('test_path', get_include_path());
        $this->_loader->addIncludePath('test_path');
        $this->assertStringStartsWith('test_path' . PATH_SEPARATOR, get_include_path());

        $this->assertNotContains('my_test_path', get_include_path());
        $this->_loader->addIncludePath(array('my_test_path'));
        $this->assertStringStartsWith('my_test_path' . PATH_SEPARATOR, get_include_path());
    }

    public function testAddFilesMap()
    {
        $this->assertFalse(class_exists('Test_Magento_Autoload_Map', false));
        $this->_loader->addFilesMap(
            array('Test_Magento_Autoload_Map' => 'dev/tests/unit/testsuite/Magento/Autoload/TestMap.php',)
        );
        $reflection = new ReflectionObject(new Test_Magento_Autoload_Map());
        $this->assertStringEndsWith('TestMap.php', $reflection->getFilename());

        $this->assertFalse(class_exists('Unit_TestClassMapClass', false));
        $this->_loader->addFilesMap(__DIR__ . '/Autoload/.classmap.php');
        $reflection = new ReflectionObject(new Unit_TestClassMapClass());
        $this->assertStringEndsWith('TestClassMapInFile.php', $reflection->getFilename());
    }

    /**
     * @expectedException Exception
     */
    public function testAddFilesMapWrongFile()
    {
        $this->_loader->addFilesMap('not_existing_file.php');
    }

    /**
     * @expectedException Exception
     */
    public function testAddFilesMapWrongParam()
    {
        $this->_loader->addFilesMap(new stdClass());
    }
}

