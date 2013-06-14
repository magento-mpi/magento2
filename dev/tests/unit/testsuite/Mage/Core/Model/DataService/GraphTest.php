<?php
/**
 * Mage_Core_Model_DataService_Invoker
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Core_Model_DataService_GraphTest extends PHPUnit_Framework_TestCase
{
    /**
     * Fake info for service and classes.
     */
    const TEST_CLASS_NAME = 'TEST_CLASS_NAME';

    const TEST_DATA_SERVICE_NAME = 'TEST_DATA_SERVICE_NAME';

    const TEST_NAMESPACE = 'TEST_NAMESPACE';

    const TEST_NAMESPACE_ALIAS = 'TEST_NAMESPACE_ALIAS';

    /**
     * @var Mage_Core_Model_DataService_Graph
     */
    protected $_graph;

    /**
     * @var object $_dataServiceMock
     */
    protected $_dataServiceMock;

    /**
     * @var  PHPUnit_Framework_MockObject_MockObject
     */
    protected $_factoryMock;

    /**
     * @var  PHPUnit_Framework_MockObject_MockObject
     */
    protected $_repositoryMock;

    /**
     * @return mixed
     */
    public function retrieveMethod()
    {
        return $this->_dataServiceMock;
    }

    public function setup()
    {
        $this->_factoryMock = $this->getMock('Mage_Core_Model_DataService_Invoker', array(), array(), "", false);
        $this->_repositoryMock = $this->getMock('Mage_Core_Model_DataService_Repository', array(), array(), "", false);
        $this->_graph = new Mage_Core_Model_DataService_Graph($this->_factoryMock, $this->_repositoryMock);
        $this->_dataServiceMock = (object)array();
    }

    public function testInit()
    {
        $this->_repositoryMock->expects($this->once())->method('addNameInNamespace')->with(
            self::TEST_NAMESPACE,
            self::TEST_DATA_SERVICE_NAME,
            self::TEST_NAMESPACE_ALIAS
        );
        $namespaceConfig
            = array('namespaces' => array(self::TEST_NAMESPACE =>
                                          Mage_Core_Model_DataService_GraphTest::TEST_NAMESPACE_ALIAS));
        $this->_repositoryMock->expects($this->once())->method("get")->with(
            $this->equalTo(self::TEST_DATA_SERVICE_NAME)
        )->will($this->returnValue(null));
        $this->_factoryMock->expects($this->once())->method('getServiceData')->with(
            $this->equalTo(self::TEST_DATA_SERVICE_NAME)
        )->will($this->returnValue($this->_dataServiceMock));
        $this->_repositoryMock->expects($this->once())->method("add")->with(
            $this->equalTo(self::TEST_DATA_SERVICE_NAME),
            $this->equalTo($this->_dataServiceMock)
        );
        $this->_graph->init(
            array(self::TEST_DATA_SERVICE_NAME => $namespaceConfig)
        );
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Data reference configuration doesn't have a block to link to
     */
    public function testInitMissingNamespaces()
    {
        $namespaceConfig = array();
        $this->_repositoryMock->expects($this->any())->method("get")->with(
            $this->equalTo(self::TEST_DATA_SERVICE_NAME)
        )->will($this->returnValue(null));
        $this->_factoryMock->expects($this->any())->method('getServiceData')->with(
            $this->equalTo(self::TEST_DATA_SERVICE_NAME)
        )->will($this->returnValue($this->_dataServiceMock));
        $this->_repositoryMock->expects($this->any())->method("add")->with(
            $this->equalTo(self::TEST_DATA_SERVICE_NAME),
            $this->equalTo($this->_dataServiceMock)
        );
        $this->_graph->init(
            array(self::TEST_DATA_SERVICE_NAME => $namespaceConfig)
        );
    }

    public function testGet()
    {
        $this->_dataServiceMock = (object)array();
        $this->_repositoryMock->expects($this->once())->method("get")->with(
            $this->equalTo(self::TEST_DATA_SERVICE_NAME)
        )->will($this->returnValue($this->_dataServiceMock));
        $this->assertEquals(
            $this->_dataServiceMock,
            $this->_graph->get(self::TEST_DATA_SERVICE_NAME)
        );
    }

    public function testGetChild()
    {
        $this->_dataServiceMock = (object)array();
        $this->_repositoryMock->expects($this->once())->method("get")->with(
            $this->equalTo(self::TEST_DATA_SERVICE_NAME)
        )->will($this->returnValue($this->_dataServiceMock));
        $this->assertEquals(
            $this->_dataServiceMock,
            $this->_graph->getChildNode(self::TEST_DATA_SERVICE_NAME)
        );
    }

    public function testGetByNamespace()
    {
        $this->_repositoryMock->expects($this->once())->method('getByNamespace')->with(
            self::TEST_NAMESPACE
        )->will($this->returnValue(self::TEST_DATA_SERVICE_NAME));
        $this->assertEquals(
            self::TEST_DATA_SERVICE_NAME,
            $this->_graph->getByNamespace(self::TEST_NAMESPACE)
        );
    }

    public function testGetArgumentValue()
    {
        $this->_factoryMock->expects($this->once())->method('getArgumentValue')->with(
            $this->equalTo(self::TEST_DATA_SERVICE_NAME)
        )->will($this->returnValue($this->_dataServiceMock));

        $argValue = $this->_graph->getArgumentValue(self::TEST_DATA_SERVICE_NAME);

        $this->assertEquals($this->_dataServiceMock, $argValue);
    }
}