<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

require_once realpath(__DIR__ . '/../../../../../../../') . '/tools/Di/Code/Scanner/ScannerInterface.php';
require_once realpath(__DIR__ . '/../../../../../../../') . '/tools/Di/Code/Scanner/FileScanner.php';
require_once realpath(__DIR__ . '/../../../../../../../') . '/tools/Di/Code/Scanner/XmlScanner.php';

class Magento_Tools_Di_Code_Scanner_XmlScannerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento\Tools\Di\Code\Scanner\XmlScanner
     */
    protected $_model;

    /**
     * @var string
     */
    protected $_testDir;

    /**
     * @var array
     */
    protected $_testFiles = array();

    protected function setUp()
    {
        $this->_model = new Magento\Tools\Di\Code\Scanner\XmlScanner();
        $this->_testDir = str_replace('\\', '/', realpath(__DIR__ . '/../../') . '/_files');
        $this->_testFiles =  array(
            $this->_testDir . '/app/code/Magento/SomeModule/etc/adminhtml/system.xml',
            $this->_testDir . '/app/code/Magento/SomeModule/etc/di.xml',
            $this->_testDir . '/app/code/Magento/SomeModule/view/frontend/layout.xml',
            $this->_testDir . '/app/etc/di/config.xml'

        );
    }

    public function testCollectEntities()
    {
        $actual = $this->_model->collectEntities($this->_testFiles);
        $expected = array(
            'Magento_Backend_Block_System_Config_Form_Fieldset_Modules_DisableOutput_Proxy',
            'Magento_Core_Model_App_Proxy',
            'Magento_Core_Model_Cache_Proxy',
            'Magento_Backend_Block_Menu_Proxy',
            'Magento_Core_Model_StoreManager_Proxy',
            'Magento_Core_Model_Layout_Factory',
        );
        $this->assertEquals($expected, $actual);
    }
}
