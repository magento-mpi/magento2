<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Autoload_ClassMapTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Autoload_ClassMap
     */
    protected $_loader = null;

    public function setUp()
    {
        $this->_loader = new Magento_Autoload_ClassMap(__DIR__ . '/_files');
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testConstructNonExistent()
    {
        new Magento_Autoload_ClassMap('non_existent');
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testConstructNotDir()
    {
        new Magento_Autoload_ClassMap(__FILE__);
    }

    public function testGetFileAddMap()
    {

        $this->assertFalse($this->_loader->getFile('TestMap'));
        $this->assertFalse($this->_loader->getFile('Non_Existent_Class'));
        $this->assertSame($this->_loader, $this->_loader->addMap(array('TestMap' => 'TestMap.php')));
        $this->assertFileExists($this->_loader->getFile('TestMap'));
        $this->assertFalse($this->_loader->getFile('Non_Existent_Class'));
    }

    public function testLoad()
    {
        $this->_loader->addMap(array('TestMap' => 'TestMap.php', 'Unknown_Class' => 'invalid_file.php'));
        $this->assertFalse(class_exists('TestMap', false));
        $this->assertFalse(class_exists('Unknown_Class', false));
        $this->_loader->load('TestMap');
        $this->_loader->load('Unknown_Class');
        $this->assertTrue(class_exists('TestMap', false));
        $this->assertFalse(class_exists('Unknown_Class', false));
    }
}
