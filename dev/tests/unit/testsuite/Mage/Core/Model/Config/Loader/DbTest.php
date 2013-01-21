<?php
/**
 * {license_notice}
 *
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Mage_Core_Model_Config_Loader_Db
 */
class Mage_Core_Model_Config_Loader_DbTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Core_Model_Config_Loader_Db
     */
    protected $_model;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_dbUpdaterMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_modulesConfigMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_resourceMock;

    protected function setUp()
    {
        $this->markTestIncomplete('MAGETWO-6406');
        $this->_modulesConfigMock = $this->getMock('Mage_Core_Model_Config_Modules',
            array(), array(), '', false, false
        );
        $this->_dbUpdaterMock = $this->getMock('Mage_Core_Model_Db_UpdaterInterface',
            array(), array(), '', false, false
        );
        $this->_resourceMock = $this->getMock('Mage_Core_Model_Resource_Config', array(), array(), '', false, false);
        $this->_model = new Mage_Core_Model_Config_Loader_Db(
            $this->_modulesConfigMock,
            $this->_resourceMock,
            $this->_dbUpdaterMock
        );
    }

    protected function tearDown()
    {
        unset($this->_dbUpdaterMock);
        unset($this->_modulesConfigMock);
        unset($this->_resourceMock);
        unset($this->_model);
    }

    public function testLoad()
    {
        $this->_dbUpdaterMock->expects($this->once())->method('updateScheme');

        $configMock = $this->getMock('Mage_Core_Model_Config_Base', array(), array(), '', false, false);
        $configMock->expects($this->once())->method('extend') ->with($this->_modulesConfigMock);

        $this->_resourceMock->expects($this->once())->method('loadToXml')->with($configMock);

        $this->_model->load($configMock);
    }
}
