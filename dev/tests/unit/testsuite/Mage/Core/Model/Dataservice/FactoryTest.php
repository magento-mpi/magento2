<?php
/**
 * Test class for Mage_Core_Model_Dataservice_Factory
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Core_Model_Dataservice_FactoryTest extends PHPUnit_Framework_TestCase
{
    const TEST_CLASS_NAME = 'TEST_CLASS_NAME';

    const TEST_DATA_SERVICE_NAME = 'TEST_DATA_SERVICE_NAME';

    const TEST_NAMESPACE = 'TEST_NAMESPACE';

    const TEST_NAMESPACE_ALIAS = 'TEST_NAMESPACE_ALIAS';

    /** @var Mage_Core_Model_Dataservice_Factory */
    protected $_factory;

    /** @var Mage_Core_Model_Dataservice_Config_Interface */
    protected $_configMock;

    /** @var  Magento_ObjectManager */
    protected $_objectManagerMock;

    /** @var  Mage_Core_Model_Dataservice_Path_Composite */
    protected $_compositeMock;

    /** @var  Mage_Core_Model_Dataservice_Path_Navigator */
    protected $_pathNavigatorMock;

    protected $_dataserviceMock;

    public function retrieveMethod()
    {
        return $this->_dataserviceMock;
    }

    public function setup()
    {
        $this->_configMock = $this->getMock(
            'Mage_Core_Model_Dataservice_Config_Interface', array(), array(), "", false
        );
        $this->_objectManagerMock = $this->getMock('Magento_ObjectManager', array(), array(), "", false);
        $this->_compositeMock = $this->getMock(
            'Mage_Core_Model_Dataservice_Path_Composite', array(), array(), "", false
        );
        $this->_pathNavigatorMock = $this->getMockBuilder('Mage_Core_Model_Dataservice_Path_Navigator')
            ->disableOriginalConstructor()->getMock();
        $this->_factory = new Mage_Core_Model_Dataservice_Factory(
            $this->_configMock,
            $this->_objectManagerMock,
            $this->_compositeMock,
            $this->_pathNavigatorMock);
        $this->_dataserviceMock = (object)array();
    }

    public function testGetArgumentValue()
    {
        $path = 'path';
        $result = 'result';
        $pathArray = array($path);
        $this->_pathNavigatorMock->expects($this->once())->method('search')->with($this->_compositeMock, $pathArray)
            ->will(
            $this->returnValue($result)
        );
        $this->assertEquals($result, $this->_factory->getArgumentValue($path));
    }

    public function testCreateDataservice()
    {
        $classInformation = array('class'          => self::TEST_CLASS_NAME,
                                  'retrieveMethod' => 'retrieveMethod', 'methodArguments' => array());
        $this->_configMock->expects($this->once())->method("getClassByAlias")->with(
            $this->equalTo(self::TEST_DATA_SERVICE_NAME)
        )->will($this->returnValue($classInformation));
        $this->_objectManagerMock->expects($this->once())->method("create")->with(
            $this->equalTo(self::TEST_CLASS_NAME)
        )->will($this->returnValue($this));
        $this->assertSame($this->_dataserviceMock, $this->_factory->createDataservice(self::TEST_DATA_SERVICE_NAME));
    }

    public function testCreateDataserviceWithArguments()
    {
        $classInformation = array('class'          => self::TEST_CLASS_NAME,
            'retrieveMethod' => 'retrieveMethod', 'methodArguments' => array('something'));
        $this->_configMock->expects($this->once())->method("getClassByAlias")->with(
            $this->equalTo(self::TEST_DATA_SERVICE_NAME)
        )->will($this->returnValue($classInformation));
        $this->_objectManagerMock->expects($this->once())->method("create")->with(
            $this->equalTo(self::TEST_CLASS_NAME)
        )->will($this->returnValue($this));
        $this->assertSame($this->_dataserviceMock, $this->_factory->createDataservice(self::TEST_DATA_SERVICE_NAME));
    }
}