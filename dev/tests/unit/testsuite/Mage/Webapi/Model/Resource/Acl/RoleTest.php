<?php
/**
 * Test class for Mage_Webapi_Model_Resource_Acl_Role
 *
 * @copyright {}
 */
class Mage_Webapi_Model_Resource_Acl_RoleTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Test_Helper_ObjectManager
     */
    protected $_helper;

    /**
     * @var Mage_Webapi_Helper_Data
     */
    protected $_helperData;

    /**
     * @var Magento_ObjectManager|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_objectManager;

    /**
     * @var Mage_Core_Model_Resource|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_resource;

    /**
     * @var Varien_Db_Adapter_Pdo_Mysql|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_adapter;

    protected function setUp()
    {
        $this->_helper = new Magento_Test_Helper_ObjectManager($this);

        $this->_helperData = $this->getMockBuilder('Mage_Webapi_Helper_Data')
            ->disableOriginalConstructor()
            ->setMethods(array('__'))
            ->getMock();
        $this->_helperData->expects($this->any())->method('__')->will($this->returnArgument(0));

        $this->_objectManager = $this->getMockBuilder('Magento_ObjectManager')
            ->disableOriginalConstructor()
            ->setMethods(array('create'))
            ->getMockForAbstractClass();
    }

    /**
     * Create resource model
     *
     * @param Varien_Db_Select $selectMock
     * @return Mage_Webapi_Model_Resource_Acl_Role
     */
    protected function _createModel($selectMock = null)
    {
        $this->_resource = $this->getMockBuilder('Mage_Core_Model_Resource')
            ->disableOriginalConstructor()
            ->setMethods(array('getConnection', 'getTableName'))
            ->getMock();

        $this->_resource->expects($this->any())
            ->method('getTableName')
            ->withAnyParameters()
            ->will($this->returnArgument(0));

        $this->_adapter = $this->getMockBuilder('Varien_Db_Adapter_Pdo_Mysql')
            ->disableOriginalConstructor()
            ->setMethods(array('select', 'fetchCol', 'fetchPairs'))
            ->getMock();

        $this->_adapter->expects($this->any())
            ->method('fetchCol')
            ->withAnyParameters()
            ->will($this->returnValue(array(1)));

        $this->_adapter->expects($this->any())
            ->method('fetchPairs')
            ->withAnyParameters()
            ->will($this->returnValue(array('key' => 'value')));

        if (!$selectMock) {
            $selectMock = new Varien_Db_Select(
                $this->getMock('Varien_Db_Adapter_Pdo_Mysql', array(), array(), '', false));
        }

        $this->_adapter->expects($this->any())
            ->method('select')
            ->withAnyParameters()
            ->will($this->returnValue($selectMock));

        $this->_resource->expects($this->any())
            ->method('getConnection')
            ->withAnyParameters()
            ->will($this->returnValue($this->_adapter));

        return $this->_helper->getModel('Mage_Webapi_Model_Resource_Acl_Role', array(
            'resource' => $this->_resource,
            'helper' => $this->_helperData
        ));
    }

    /**
     * Test constructor
     */
    public function testConstructor()
    {
        $model = $this->_createModel();

        $this->assertAttributeEquals('webapi_role', '_mainTable', $model);
        $this->assertAttributeEquals('role_id', '_idFieldName', $model);
    }

    /**
     * Test _initUniqueFields()
     */
    public function testGetUniqueFields()
    {
        $model = $this->_createModel();
        $fields = $model->getUniqueFields();

        $this->assertEquals(array(array('field' => 'role_name', 'title' => 'Role Name')), $fields);
    }

    /**
     * Test getRolesList()
     */
    public function testGetRolesList()
    {
        $selectMock = $this->getMockBuilder('Varien_Db_Select')
            ->setConstructorArgs(array($this->getMock('Varien_Db_Adapter_Pdo_Mysql', array(), array(), '', false)))
            ->setMethods(array('from', 'order'))
            ->getMock();

        $selectMock->expects($this->once())
            ->method('from')
            ->with('webapi_role', array('role_id', 'role_name'))
            ->will($this->returnSelf());

        $selectMock->expects($this->once())
            ->method('order')
            ->with('role_name')
            ->will($this->returnSelf());

        $model = $this->_createModel($selectMock);
        $result = $model->getRolesList();
        $this->assertEquals(array('key' => 'value'), $result);
    }

    /**
     * Test getRolesIds()
     */
    public function testGetRolesIds()
    {
        $selectMock = $this->getMockBuilder('Varien_Db_Select')
            ->setConstructorArgs(array($this->getMock('Varien_Db_Adapter_Pdo_Mysql', array(), array(), '', false)))
            ->setMethods(array('from', 'order'))
            ->getMock();

        $selectMock->expects($this->once())
            ->method('from')
            ->with('webapi_role', array('role_id'))
            ->will($this->returnSelf());

        $model = $this->_createModel($selectMock);

        $result = $model->getRolesIds();
        $this->assertEquals(array(1), $result);
    }
}
