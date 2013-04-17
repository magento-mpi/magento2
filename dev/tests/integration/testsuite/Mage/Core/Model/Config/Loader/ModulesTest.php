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
     * @expectedException Magento_Exception
     * @expectedExceptionMessage The module 'Mage_Core' cannot be enabled without PHP extension 'fixture'
     */
    public function testLoadMissingExtension()
    {
        $dir = new Mage_Core_Model_Dir(
            $this->getMock('Magento_Filesystem', array(), array(), '', false),
            __DIR__ . '/_files',
            array(),
            array(Mage_Core_Model_Dir::MODULES => __DIR__ . '/_files')
        );
        $loader = new Mage_Core_Model_Config_Loader_Modules(
            $this->getMock('Mage_Core_Model_Config_Primary', array(), array(), '', false),
            $dir,
            $this->getMock('Mage_Core_Model_Config_BaseFactory', array(), array(), '', false),
            $this->getMock('Mage_Core_Model_Config_Resource', array(), array(), '', false),
            $this->getMock('Mage_Core_Model_Config_Loader_Modules_File', array(), array(), '', false),
            $this->getMock('Magento_ObjectManager', array(), array(), '', false)
        );
        $config = new Mage_Core_Model_Config_Base('<config><modules/><global><di/></global></config>');
        $loader->load($config);
    }
}
