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
    protected $_transFactoryMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_configDataFactory;

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
        $this->_transFactoryMock = $this->getMock(
            'Mage_Core_Model_Resource_Transaction_Factory', array(), array(), '', false
        );
        $this->_configDataFactory = $this->getMock('Mage_Core_Model_Config_Data_Factory', array(), array(), '', false);
        $this->_appConfigMock = $this->getMock('Mage_Core_Model_Config', array(), array(), '', false);
        $this->_applicationMock = $this->getMock('Mage_Core_Model_App', array(), array(), '', false);

        $this->_model = new Mage_Backend_Model_Config(
            $this->_applicationMock,
            $this->_appConfigMock,
            $this->_eventManagerMock,
            $structureMock,
            $this->_transFactoryMock,
            $this->_configDataFactory
        );
    }

    public function testSaveDoesNotDoAnythingIfGroupsAreNotPassed()
    {
        $this->_configDataFactory->expects($this->never())->method('create');
        $this->_model->save();
    }

    public function testSaveEmptiesNonSetArguments()
    {
        $this->_structureReaderMock->expects($this->never())->method('getConfiguration');
        $this->assertNull($this->_model->getSection());
        $this->assertNull($this->_model->getWebsite());
        $this->assertNull($this->_model->getStore());
        $this->_model->save();
        $this->assertSame('', $this->_model->getSection());
        $this->assertSame('', $this->_model->getWebsite());
        $this->assertSame('', $this->_model->getStore());
    }

    public function testSave()
    {
        $this->_model->setSection('section');
        $this->_model->setGroups(array('group_1' => array()));
        $this->_model->setStore('store');
        $this->_model->setWebsite('website');
        $this->_model->setScope('scope');
        $this->_model->setScopeId('scopeID');

    }
}
