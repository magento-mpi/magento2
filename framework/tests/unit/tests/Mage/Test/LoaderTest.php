<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     functional_test
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Test_LoaderTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var string
     */
    protected $_includeDirs;

    /**
     * @var Mage_Test_Loader
     */
    protected $_loader;

    protected function setUp()
    {
        $this->_loader = new Mage_Test_Loader();
        $this->_includeDirs = set_include_path(__DIR__ . '/_files');
    }

    protected function tearDown()
    {
        set_include_path($this->_includeDirs);
    }

    public function testLoad()
    {
         $className = 'ProTestSuite';
         $suite = $this->_loader->load($className);
         $this->assertInstanceOf('ReflectionClass', $suite);
         $this->assertEquals($className, $suite->getName());
    }

    /**
     * @expectedException RuntimeException
     */
    public function testLoadNotExists()
    {
        $this->_loader->load('not_exists_class_');
    }
}