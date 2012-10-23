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

class Mage_Backend_Model_ConfigTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Backend_Model_Config
     */
    protected $_model;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_eventManagerMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_structureReaderMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_transactionFactoryMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_objectFactoryMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_appConfigMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_applicationMock;

    public function setUp()
    {
        $this->_eventManagerMock = $this->getMock('Mage_Core_Model_Event_Manager', array(), array(), '', false);
        $this->_structureReaderMock = $this->getMock(
            'Mage_Backend_Model_Config_Structure_Reader', array(), array(), '', false
        );
        $structureMock = $this->getMock('Mage_Backend_Model_Config_Structure', array(), array(), '', false);
        $this->_structureReaderMock->expects($this->any())->method('getConfiguration')->will(
            $this->returnValue($structureMock)
        );
        $this->_transactionFactoryMock = $this->getMock(
            'Mage_Backend_Model_Resource_Transaction_Factory', array(), array(), '', false
        );
        $this->_objectFactoryMock = $this->getMock('Mage_Core_Model_Config', array(), array(), '', false);
        $this->_appConfigMock = $this->getMock('Mage_Core_Model_Config', array(), array(), '', false);
        $this->_applicationMock = $this->getMock('Mage_Core_Model_Application', array(), array(), '', false);

        $this->_model = new Mage_Backend_Model_Config(array(
            'eventManager' => $this->_eventManagerMock,
            'structureReader' => $this->_structureReaderMock,
            'transactionFactory' => $this->_transactionFactoryMock,
            'objectFactory' => $this->_objectFactoryMock,
            'applicationConfig' => $this->_appConfigMock,
            'application' => $this->_appConfigMock
        ));
    }

    public function testSaveDoesNotDoAnythingIfGroupsAreNotPassed()
    {
        $this->_structureReaderMock->expects($this->never())->method('getConfiguration');
        $this->_model->save();
    }

    public function testSaveEmptiesNonSetArguments()
    {
        $this->_structureReaderMock->expects($this->never())->method('getConfiguration');
        $this->assertNull($this->_model->getSection());
        $this->assertNull($this->_model->getWebsite());
        $this->assertNull($this->_model->getStore());
        $this->_model->save();
        $this->assertTrue('' === $this->_model->getSection());
        $this->assertTrue('' === $this->_model->getWebsite());
        $this->assertTrue('' === $this->_model->getStore());
    }
}
