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

    protected function setUp()
    {
        $this->_helper = new Magento_Test_Helper_ObjectManager($this);

        $this->_helperData = $this->getMock('Mage_Webapi_Helper_Data', array('__'));
        $this->_helperData->expects($this->any())->method('__')->will($this->returnArgument(0));

        $this->_objectManager = $this->getMockBuilder('Magento_ObjectManager')
            ->disableOriginalConstructor()
            ->setMethods(array('create'))
            ->getMockForAbstractClass();

        $this->_resource = $this->getMockBuilder('Mage_Core_Model_Resource')
            ->disableOriginalConstructor()
            ->setMethods(array('getConnection', 'getTableName'))
            ->getMock();

        $this->_resource->expects($this->any())
            ->method('getTableName')
            ->withAnyParameters()
            ->will($this->returnArgument(0));

        $adapter = $this->getMockBuilder('Varien_Db_Adapter_Pdo_Mysql')
            ->disableOriginalConstructor()
            ->setMethods(array('select', 'fetchCol', 'fetchPairs'))
            ->getMock();

        $adapter->expects($this->any())
            ->method('select')
            ->withAnyParameters()
            ->will($this->returnValue(new Varien_Db_Select($this->getMock('Varien_Db_Adapter_Pdo_Mysql', array(), array(), '', false))));

        $adapter->expects($this->any())
            ->method('fetchCol')
            ->withAnyParameters()
            ->will($this->returnArgument(0));

        $adapter->expects($this->any())
            ->method('fetchPairs')
            ->withAnyParameters()
            ->will($this->returnArgument(0));

        $this->_resource->expects($this->any())
            ->method('getConnection')
            ->withAnyParameters()
            ->will($this->returnValue($adapter));
    }

    /**
     * Create resource model
     *
     * @return Mage_Webapi_Model_Resource_Acl_Role
     */
    protected function _createModel()
    {
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
        $model = $this->_createModel();

        /** @var Varien_Db_Select $select */
        $select = $model->getRolesList();
        $from = $select->getPart('from');

        $this->assertEquals(array('webapi_role' =>
            array('joinCondition' => null,
                'joinType' => 'from',
                'schema' => null,
                'tableName' => 'webapi_role',
            )), $from);
    }

    /**
     * Test getRolesIds()
     */
    public function testGetRolesIds()
    {
        $model = $this->_createModel();

        /** @var Varien_Db_Select $select */
        $select = $model->getRolesIds();
        $from = $select->getPart('from');

        $this->assertEquals(array('webapi_role' =>
            array('joinCondition' => null,
                'joinType' => 'from',
                'schema' => null,
                'tableName' => 'webapi_role',
            )), $from);
    }
}
