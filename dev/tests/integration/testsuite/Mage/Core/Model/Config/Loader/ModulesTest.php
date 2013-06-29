<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Core_Model_Config_Loader_ModulesTest extends PHPUnit_Framework_TestCase
{
    /**
     * @param string $path
     */
    protected function _loadModule($path)
    {
        $dir = new Mage_Core_Model_Dir(
            __DIR__ . $path,
            array(),
            array(Mage_Core_Model_Dir::MODULES => __DIR__ . $path)
        );
        $loader = new Mage_Core_Model_Config_Loader_Modules(
            $this->getMock('Mage_Core_Model_Config_Primary', array(), array(), '', false),
            $dir,
            $this->getMock('Mage_Core_Model_Config_BaseFactory', array(), array(), '', false),
            $this->getMock('Mage_Core_Model_Config_Resource', array(), array(), '', false),
            $this->getMock('Mage_Core_Model_Config_Loader_Modules_File', array(), array(), '', false),
            $this->getMock('Magento_ObjectManager', array(), array(), '', false),
            $this->getMock('Mage_Core_Model_Config_Modules_SortedFactory', array(), array(), '', false),
            $this->getMock('Mage_Core_Model_Config_Loader_Local', array(), array(), '', false)
        );
        $config = new Mage_Core_Model_Config_Base('<config><modules/><global><di/></global></config>');
        $loader->load($config);
    }

    /**
     * @expectedException Magento_Exception
     * @expectedExceptionMessage The module 'Mage_Core' cannot be enabled without PHP extension 'fixture'
     */
    public function testLoadMissingExtension()
    {
        $this->_loadModule('/_files/single');
    }

    /**
     * @expectedException Magento_Exception
     * @expectedExceptionMessage The module 'Mage_Core' cannot be enabled. One of PHP extensions: 'version - v.1'
     */
    public function testLoadMissingExtensions()
    {
        $this->_loadModule('/_files/any');
    }
}
