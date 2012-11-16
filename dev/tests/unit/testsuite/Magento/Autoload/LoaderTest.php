<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Autoload_LoaderTest extends PHPUnit_Framework_TestCase
{
    /**
     * @expectedException InvalidArgumentException
     */
    public function testConstructException()
    {
        new Magento_Autoload_Loader('');
    }

    public function testLoad()
    {
        $this->assertFalse(defined('TEST_FIXTURE'));
        $loader = new Magento_Autoload_Loader(array($this, 'constantCallback'));
        $fixture = uniqid();
        $loader->load($fixture);
        $this->assertTrue(defined('TEST_FIXTURE'));
        $this->assertEquals($fixture, TEST_FIXTURE);
    }

    public function testLoadEmpty()
    {
        $loader = new Magento_Autoload_Loader(array($this, 'dummyCallback'));
        $loader->load('dummy');
    }

    /**
     * @throws Exception
     */
    public function testRegister()
    {
        $initialStack = spl_autoload_functions();
        $loader = new Magento_Autoload_Loader(array($this, 'dummyCallback'));
        $loader->register();
        try {
            $stack = spl_autoload_functions();
            $item = array_pop($stack);
            $this->assertArrayHasKey(0, $item);
            $this->assertArrayHasKey(1, $item);
            $this->assertSame($loader, $item[0]);
            $this->assertEquals('load', $item[1]);
            spl_autoload_unregister(array($loader, 'load'));
        } catch (Exception $e) {
            spl_autoload_unregister(array($loader, 'load'));
            throw $e;
        }
        $this->assertSame($initialStack, spl_autoload_functions());
    }

    /**
     * A callback that returns a file that declares a test constant with fixture value
     *
     * @param string $class
     * @return string
     */
    public function constantCallback($class)
    {
        unset($class); // the variable is used just to verify interface
        return __DIR__ . '/_files/constant.php';
    }

    /**
     * A dummy callback that does nothing
     */
    public function dummyCallback()
    {
        // do nothing
    }
}
