<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Core
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Core_Model_Config_OptionsTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Core_Model_Config_Options
     */
    protected $_model;

    /**
     * @var array
     */
    protected $_sourceData;

    /**
     * @var array
     */
    protected $_varDir;

    protected function setUp()
    {
        $rootDir = dirname(__FILE__);
        $io = $this->getMock('Varien_Io_File', array('checkAndCreateFolder'));
        $this->_sourceData = array(
            'app_dir' => $rootDir . DIRECTORY_SEPARATOR . 'app',
            'io' => $io,
        );
        $this->_varDir = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'var';
    }

    public function testGetVarDir()
    {
        $this->_sourceData['io']->expects($this->once())
            ->method('checkAndCreateFolder')
            ->with($this->equalTo($this->_varDir))
            ->will($this->returnValue(true));

        $this->_model = new Mage_Core_Model_Config_Options($this->_sourceData);
        $result = $this->_model->getVarDir();
        $this->assertEquals($this->_varDir, $result);
    }

    public function testGetVarDirSysTmpDir()
    {
        $sysVarDir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'magento' . DIRECTORY_SEPARATOR . 'var';

        $this->_sourceData['io']->expects($this->at(0))
            ->method('checkAndCreateFolder')
            ->with($this->equalTo($this->_varDir))
            ->will($this->throwException(new Exception));

        $this->_sourceData['io']->expects($this->at(1))
            ->method('checkAndCreateFolder')
            ->with($this->equalTo($sysVarDir))
            ->will($this->returnValue(true));

        $this->_model = new Mage_Core_Model_Config_Options($this->_sourceData);
        $result = $this->_model->getVarDir();
        $this->assertEquals($sysVarDir, $result);
    }

    /**
     * @expectedException Mage_Core_Exception
     */
    public function testGetVarDirWithException()
    {
        $sysVarDir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'magento' . DIRECTORY_SEPARATOR . 'var';
        $this->_sourceData['io']->expects($this->at(0))
            ->method('checkAndCreateFolder')
            ->with($this->equalTo($this->_varDir))
            ->will($this->throwException(new Exception));

        $this->_sourceData['io']->expects($this->at(1))
            ->method('checkAndCreateFolder')
            ->with($this->equalTo($sysVarDir))
            ->will($this->throwException(new Exception));

        $this->_model = new Mage_Core_Model_Config_Options($this->_sourceData);
    }

    public function testCreateDirIfNotExists()
    {
        $checkDir = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'test';
        $this->_sourceData['io']->expects($this->at(0))
            ->method('checkAndCreateFolder')
            ->with($this->equalTo($this->_varDir))
            ->will($this->returnValue(true));

        $this->_sourceData['io']->expects($this->at(1))
            ->method('checkAndCreateFolder')
            ->with($this->equalTo($checkDir))
            ->will($this->returnValue(true));

        $this->_model = new Mage_Core_Model_Config_Options($this->_sourceData);

        $result = $this->_model->createDirIfNotExists($checkDir);
        $this->assertEquals(true, $result);
    }

    public function testCreateDirIfNotExistsNegativeResult()
    {
        $checkDir = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'dirNotExists';
        $this->_sourceData['io']->expects($this->at(0))
            ->method('checkAndCreateFolder')
            ->with($this->equalTo($this->_varDir))
            ->will($this->returnValue(true));

        $this->_sourceData['io']->expects($this->at(1))
            ->method('checkAndCreateFolder')
            ->will($this->throwException(new Exception));

        $this->_model = new Mage_Core_Model_Config_Options($this->_sourceData);
        $result = $this->_model->createDirIfNotExists($checkDir);
        $this->assertEquals(false, $result);
    }
}