<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Backend
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Backend_Model_System_Config_ReaderTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Backend_Model_System_Config_Reader
     */
    protected $_model;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_appConfigMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_cacheMock;

    public function setUp()
    {
        $this->_appConfigMock = $this->getMock('Mage_Core_Model_Config', array(), array(), '', false);
        $this->_cacheMock = $this->getMock('Mage_Core_Model_Cache', array(), array(), '', false);
        $this->_cacheMock->expects($this->any())->method('canUse')->will($this->returnValue(true));

        $this->_model = new Mage_Backend_Model_System_Config_Reader(array(
            'config' => $this->_appConfigMock,
            'cache' => $this->_cacheMock
        ));
    }

    public function testGetConfigurationLoadsConfigFromCacheWhenCacheIsEnabled()
    {

        $cachedObject = new StdClass();
        $cachedObject->foo = 'bar';
        $cachedData = serialize($cachedObject);

        $this->_cacheMock->expects($this->once())->method('load')
            ->with(Mage_Backend_Model_System_Config_Reader::CACHE_SYSTEM_CONFIGURATION)
            ->will($this->returnValue($cachedData));

        $this->assertEquals($cachedObject, $this->_model->getConfiguration());
    }

    public function testGetConfigurationLoadsConfigFromFilesAndCachesIt()
    {
        $this->_cacheMock->expects($this->once())->method('load')->will($this->returnValue(false));

        $testFiles = array('file1', 'file2');

        $this->_appConfigMock->expects($this->once())
            ->method('getModuleConfigurationFiles')
            ->will($this->returnValue($testFiles));

        $configMock = new StdClass();
        $configMock->foo = "bar";

        $this->_appConfigMock->expects($this->once())
            ->method('getModelInstance')
            ->with('Mage_Backend_Model_System_Config', $testFiles)
            ->will($this->returnValue($configMock));

        $this->_cacheMock->expects($this->once())->method('save')->with(
            $this->isType('string')
        );

        $this->assertEquals($configMock, $this->_model->getConfiguration());
    }
}
