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

    public function testGetFileAddMap()
    {
        $locator = new Magento_Autoload_ClassMap(__DIR__ . '/_files');
        $this->assertFalse($locator->getFile('TestMap'));
        $this->assertFalse($locator->getFile('Non_Existent_Class'));
        $this->assertSame($locator, $locator->addMap(array('TestMap' => 'TestMap.php')));
        $this->assertFileExists($locator->getFile('TestMap'));
        $this->assertFalse($locator->getFile('Non_Existent_Class'));
    }
}
