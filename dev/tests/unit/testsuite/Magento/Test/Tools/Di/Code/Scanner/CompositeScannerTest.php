<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

require_once realpath(dirname(__FILE__) . '/../../../../../../../../../') . '/tools/Magento/Tools/Di/Code/Scanner/ScannerInterface.php';
require_once realpath(dirname(__FILE__) . '/../../../../../../../../../') . '/tools/Magento/Tools/Di/Code/Scanner/CompositeScanner.php';

class Magento_Test_Tools_Di_Code_Scanner_CompositeScannerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento\Tools\Di\Code\Scanner\CompositeScanner
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model = new Magento\Tools\Di\Code\Scanner\CompositeScanner();
    }

    public function testScan()
    {
        $phpFiles = array(
            'one/file/php',
            'two/file/php',
        );
        $configFiles = array(
            'one/file/config',
            'two/file/config',
        );
        $files = array(
            'php' => $phpFiles,
            'config' => $configFiles,
        );

        $scannerPhp = $this->getMock('Magento\Tools\Di\Code\Scanner\ScannerInterface');
        $scannerXml = $this->getMock('Magento\Tools\Di\Code\Scanner\ScannerInterface');

        $scannerPhp->expects($this->once())
            ->method('collectEntities')
            ->with($phpFiles)
            ->will($this->returnValue(array('Model_OneProxy', 'Model_TwoFactory')));

        $scannerXml->expects($this->once())
            ->method('collectEntities')
            ->with($configFiles)
            ->will($this->returnValue(array('Model_OneProxy', 'Model_ThreeFactory')));

        $this->_model->addChild($scannerPhp, 'php');
        $this->_model->addChild($scannerXml, 'config');

        $actual = $this->_model->collectEntities($files);
        $expected = array('Model_OneProxy', 'Model_TwoFactory', 'Model_ThreeFactory');

        $this->assertEquals($expected, array_values($actual));
    }
}
