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

    public function testAutoloadAddMap()
    {
        $loader = new Magento_Autoload_ClassMap(__DIR__);
        $this->assertFalse(class_exists('TestMap', false));
        $this->assertFalse(class_exists('Non_Existent_Class', false));
        $this->assertSame($loader, $loader->addMap(array('TestMap' => 'TestMap.php')));
        $loader->autoload('TestMap');
        $loader->autoload('Non_Existent_Class');
        $this->assertTrue(class_exists('TestMap', false));
        $this->assertFalse(class_exists('Non_Existent_Class', false));
    }
}
