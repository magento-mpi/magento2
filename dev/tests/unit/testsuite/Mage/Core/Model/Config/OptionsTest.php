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
        $ioModel = $this->getMock('Varien_Io_File', array('checkAndCreateFolder'));
        $this->_sourceData = array(
            'app_dir' => __DIR__ . DIRECTORY_SEPARATOR . 'app',
            'io' => $ioModel,
        );
        $this->_varDir = __DIR__ . DIRECTORY_SEPARATOR . 'var';
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

    /**
     * @expectedException Mage_Core_Exception
     */
    public function testGetVarDirWithException()
    {
        $this->_sourceData['io']->expects($this->at(0))
            ->method('checkAndCreateFolder')
            ->with($this->equalTo($this->_varDir))
            ->will($this->throwException(new Exception));
        $this->_model = new Mage_Core_Model_Config_Options($this->_sourceData);
    }

    public function testCreateDirIfNotExists()
    {
        $checkDir = __DIR__ . DIRECTORY_SEPARATOR . 'test';
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
        $checkDir = __DIR__ . DIRECTORY_SEPARATOR . 'dirNotExists';
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