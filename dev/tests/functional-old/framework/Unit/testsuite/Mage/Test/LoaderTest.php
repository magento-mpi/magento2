<?php

/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
class Mage_Test_LoaderTest extends Unit_PHPUnit_TestCase
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
        $className = 'SimpleTestSuite';
        $suite = $this->_loader->load($className);
        $this->assertInstanceOf('ReflectionClass', $suite);
        $this->assertEquals($className, $suite->getName());
    }

    /**
     * @expectedException PHPUnit_Framework_Exception
     */
    public function testLoadNotExists()
    {
        $this->setExpectedException('PHPUnit_Framework_Exception', 'Cannot open file "not\exists\class.php');
        $this->_loader->load('not_exists_class');
    }
}