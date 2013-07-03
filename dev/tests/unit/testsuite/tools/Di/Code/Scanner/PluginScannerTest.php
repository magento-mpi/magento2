<?php
/**
 * {license_notice}
 * 
 * @copyright {copyright}
 * @license   {license_link}
 */
require_once realpath(dirname(__FILE__) . '/../../../../../../../') . '/tools/Di/Code/Scanner/ScannerInterface.php';
require_once realpath(dirname(__FILE__) . '/../../../../../../../') . '/tools/Di/Code/Scanner/PluginScanner.php';

class Magento_Tools_Di_Code_Scanner_PluginScannerTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        $this->_model = new Magento\Tools\Di\Code\Scanner\PluginScanner();
        $this->_testDir = str_replace('\\', '/', realpath(dirname(__FILE__) . '/../../') . '/_files');
        $this->_testFiles = array(
            $this->_testDir . '/app/code/Mage/SomeModule/etc/config.xml',
            $this->_testDir . '/app/etc/config.xml',
        );
    }

    protected function tearDown()
    {
        unset($this->_model);
    }

    public function testCollectEntities()
    {
        $actual = $this->_model->collectEntities($this->_testFiles);
        $expected = array(
            'Mage_Core_Model_Cache_TagPlugin',
            'Mage_Core_Model_Action_Plugin',
            'Enterprise_PageCache_Model_Action_Plugin',
        );
        $this->assertEquals($expected, $actual);
    }
}
