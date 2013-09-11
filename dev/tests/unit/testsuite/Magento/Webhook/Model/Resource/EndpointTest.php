<?php
/**
 * \Magento\Webhook\Model\Resource\Endpoint
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Webhook
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webhook_Model_Resource_EndpointTest extends PHPUnit_Framework_TestCase
{
    const TABLE_NAME = 'outbound_endpoint_table';

    /** @var  \Magento\Webhook\Model\Resource\Endpoint */
    private $_endpoint;

    /** @var  PHPUnit_Framework_MockObject_MockObject */
    private $_adapterMock;

    /** @var  PHPUnit_Framework_MockObject_MockObject */
    private $_selectMock;

    /** @var  PHPUnit_Framework_MockObject_MockObject */
    private $_resourceMock;

    /** @var string[] */
    private $_apiUserIds = array('api_user_id1', 'api_user_id2', 'api_user_id3');

    public function setUp()
    {
        // Select mock
        $this->_selectMock = $this->_makeMock('\Magento\DB\Select');
        // Select stubs
        $this->_selectMock->expects($this->once())
            ->method('from')
            ->with(self::TABLE_NAME, array('endpoint_id'))
            ->will($this->returnSelf());

        // Adapter mock
        $this->_adapterMock = $this->_makeMock('\Magento\DB\Adapter\Pdo\Mysql');
        // Adapter stubs
        $this->_adapterMock->expects($this->once())
            ->method('select')
            ->with()
            ->will($this->returnValue($this->_selectMock));
        $this->_adapterMock->expects($this->once())
            ->method('getTransactionLevel')
            ->with()
            ->will($this->returnValue(1));

        // Resources mock
        $this->_resourceMock = $this->_makeMock('\Magento\Core\Model\Resource');
        // Resources stubs
        $stubReturnMap = array(
            array('core_read', $this->_adapterMock),
            array('core_write', $this->_adapterMock),
        );
        $this->_resourceMock->expects($this->once())
            ->method('getConnection')
            ->will($this->returnValueMap($stubReturnMap));
        $this->_resourceMock->expects($this->once())
            ->method('getTableName')
            ->with('outbound_endpoint')
            ->will($this->returnValue(self::TABLE_NAME));

        $this->_endpoint = new \Magento\Webhook\Model\Resource\Endpoint($this->_resourceMock);
    }

    public function testGetApiUserEndpoints()
    {
        $endpoints = array('endpoint1', 'endpoint2', 'endpoint3');

        $this->_selectMock->expects($this->once())
            ->method('where')
            ->with('api_user_id IN (?)', $this->_apiUserIds)
            ->will($this->returnSelf());

        $this->_adapterMock->expects($this->once())
            ->method('fetchCol')
            ->with($this->_selectMock)
            ->will($this->returnValue($endpoints));

        $this->assertEquals($endpoints, $this->_endpoint->getApiUserEndpoints($this->_apiUserIds));
    }

    public function testGetEndpointsWithoutApiUser()
    {
        $endpoints = array('endpoint1', 'endpoint2', 'endpoint3');

        $this->_selectMock->expects($this->once())
            ->method('where')
            ->with('api_user_id IS NULL')
            ->will($this->returnSelf());

        $this->_adapterMock->expects($this->once())
            ->method('fetchCol')
            ->with($this->_selectMock)
            ->will($this->returnValue($endpoints));

        $this->assertEquals($endpoints, $this->_endpoint->getEndpointsWithoutApiUser());
    }

    /**
     * Generates a mock object of the given class
     *
     * @param string $className
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    private function _makeMock($className)
    {
        return $this->getMockBuilder($className)
            ->disableOriginalConstructor()
            ->getMock();
    }

}
