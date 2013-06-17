<?php
/**
 * Mage_Core_Model_DataService_Invoker
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Core_Model_DataService_InvokerTest extends PHPUnit_Framework_TestCase
{
    /**
     * Fake info for service and classes.
     */
    const TEST_CLASS_NAME = 'TEST_CLASS_NAME';

    const TEST_DATA_SERVICE_NAME = 'TEST_DATA_SERVICE_NAME';

    const TEST_NAMESPACE = 'TEST_NAMESPACE';

    const TEST_NAMESPACE_ALIAS = 'TEST_NAMESPACE_ALIAS';

    /**
     * @var Mage_Core_Model_DataService_Invoker
     */
    protected $_invoker;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_configMock;

    /**
     * @var  PHPUnit_Framework_MockObject_MockObject
     */
    protected $_objectManagerMock;

    /**
     * @var  PHPUnit_Framework_MockObject_MockObject
     */
    protected $_compositeMock;

    /**
     * Empty data service array
     *
     * @var array
     */
    protected $_dataServiceMock;

    /**
     * Get the data service mock
     *
     * @return array
     */
    public function retrieveMethod()
    {
        return $this->_dataServiceMock;
    }

    public function setUp()
    {
        $this->_configMock = $this->getMock(
            'Mage_Core_Model_DataService_ConfigInterface', array(), array(), "", false
        );
        $this->_objectManagerMock = $this->getMock('Magento_ObjectManager', array(), array(), "", false);
        $this->_compositeMock = $this->getMock(
            'Mage_Core_Model_DataService_Path_Composite', array(), array(), "", false
        );
        $this->_invoker = new Mage_Core_Model_DataService_Invoker(
            $this->_configMock,
            $this->_objectManagerMock,
            $this->_compositeMock
        );
        $this->_dataServiceMock = array();
    }

    public function testGetServiceData()
    {
        $classInformation = array(
            'class'          => self::TEST_CLASS_NAME,
            'retrieveMethod' => 'retrieveMethod', 'methodArguments' => array());
        $this->_configMock
            ->expects($this->once())
            ->method("getClassByAlias")
            ->with($this->equalTo(self::TEST_DATA_SERVICE_NAME))
            ->will($this->returnValue($classInformation));
        $this->_objectManagerMock
            ->expects($this->once())
            ->method("get")
            ->with($this->equalTo(self::TEST_CLASS_NAME))
            ->will($this->returnValue($this));

        $this->assertSame(
            $this->_dataServiceMock,
            $this->_invoker->getServiceData(self::TEST_DATA_SERVICE_NAME)
        );
    }

    public function testGetServiceDataWithArguments()
    {
        $classInformation = array(
            'class'          => self::TEST_CLASS_NAME,
            'retrieveMethod' => 'retrieveMethod', 'methodArguments' => array('something'));
        $this->_configMock
            ->expects($this->once())
            ->method("getClassByAlias")
            ->with($this->equalTo(self::TEST_DATA_SERVICE_NAME))
            ->will($this->returnValue($classInformation));
        $this->_objectManagerMock
            ->expects($this->once())
            ->method("get")
            ->with($this->equalTo(self::TEST_CLASS_NAME))
            ->will($this->returnValue($this));

        $this->assertSame(
            $this->_dataServiceMock,
            $this->_invoker->getServiceData(self::TEST_DATA_SERVICE_NAME)
        );
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage return an array
     */
    public function testGetServiceDataFailsIfNotArray()
    {
        // This line makes sure we don't return an array
        $this->_dataServiceMock = (object)array();
        $classInformation = array(
            'class'          => self::TEST_CLASS_NAME,
            'retrieveMethod' => 'retrieveMethod', 'methodArguments' => array());
        $this->_configMock
            ->expects($this->once())
            ->method("getClassByAlias")
            ->with($this->equalTo(self::TEST_DATA_SERVICE_NAME))
            ->will($this->returnValue($classInformation));
        $this->_objectManagerMock
            ->expects($this->once())
            ->method("get")
            ->with($this->equalTo(self::TEST_CLASS_NAME))
            ->will($this->returnValue($this));

        $this->_invoker->getServiceData(self::TEST_DATA_SERVICE_NAME);
    }
}