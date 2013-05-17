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
        $dirs = Mage::getObjectManager()->create('Mage_Core_Model_Dir', array(
            'baseDir' => array(__DIR__ . '/_files'),
            'dirs'    => array(Mage_Core_Model_Dir::MODULES => __DIR__ . '/_files'),
        ));
        $loader = Mage::getObjectManager()->create('Mage_Core_Model_Config_Loader_Modules', array(
            'dirs' => $dirs,
        ));
        $config = Mage::getObjectManager()->create('Mage_Core_Model_Config_Base', array(
            '<config><modules/><global><di/></global></config>'
        ));
        $loader->load($config);
    }
}
