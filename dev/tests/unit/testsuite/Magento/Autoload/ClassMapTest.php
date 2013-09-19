<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Autoload;

class ClassMapTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Autoload\ClassMap
     */
    protected $_loader = null;

    public function setUp()
    {
        $this->_loader = new \Magento\Autoload\ClassMap(__DIR__ . '/ClassMapTest');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testConstructNonExistent()
    {
        new \Magento\Autoload\ClassMap('non_existent');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testConstructNotDir()
    {
        new \Magento\Autoload\ClassMap(__FILE__);
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
        $this->assertTrue(class_exists('Magento\Autoload\ClassMapTest\TestMap', false));
        $this->assertFalse(class_exists('Unknown_Class', false));
    }
}
