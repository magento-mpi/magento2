<?php
/**
 * Test class for Mage_Webapi_Model_Acl_User_Factory
 *
 * @copyright {}
 */
class Mage_Webapi_Model_Acl_RoleTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Test_Helper_ObjectManager
     */
    protected $_helper;

    /**
     * @var Magento_ObjectManager|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_objectManager;

    /**
     * @var Mage_Webapi_Model_Resource_Acl_Role|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_roleResource;

    protected function setUp()
    {
        $this->_helper = new Magento_Test_Helper_ObjectManager($this);

        $this->_objectManager = $this->getMockBuilder('Magento_ObjectManager')
            ->disableOriginalConstructor()
            ->setMethods(array('create'))
            ->getMockForAbstractClass();

        $this->_roleResource = $this->getMockBuilder('Mage_Webapi_Model_Resource_Acl_Role')
            ->disableOriginalConstructor()
            ->setMethods(array('getIdFieldName', 'getReadConnection'))
            ->getMock();

        $this->_roleResource->expects($this->any())
            ->method('getIdFieldName')
            ->withAnyParameters()
            ->will($this->returnValue('id'));

        $this->_roleResource->expects($this->any())
            ->method('getReadConnection')
            ->withAnyParameters()
            ->will($this->returnValue($this->getMock('Varien_Db_Adapter_Pdo_Mysql', array(), array(), '', false)));
    }

    /**
     * Create Role model
     *
     * @param Mage_Webapi_Model_Resource_Acl_Role $roleResource
     * @param Mage_Webapi_Model_Resource_Acl_Role_Collection $resourceCollection
     * @return Mage_Webapi_Model_Acl_Role
     */
    protected function _createModel($roleResource, $resourceCollection = null)
    {
        return $this->_helper->getModel('Mage_Webapi_Model_Acl_Role', array(
            'eventDispatcher' => $this->getMock('Mage_Core_Model_Event_Manager', array(), array(), '', false),
            'cacheManager' => $this->getMock('Mage_Core_Model_Cache', array(), array(), '', false),
            'resource' => $roleResource,
            'resourceCollection' => $resourceCollection
        ));
    }

    /**
     * Test constructor
     */
    public function testConstructor()
    {
        $model = $this->_createModel($this->_roleResource);

        $this->assertAttributeEquals('Mage_Webapi_Model_Resource_Acl_Role', '_resourceName', $model);
        $this->assertAttributeEquals('id', '_idFieldName', $model);
    }

    /**
     * Test get collection and _construct
     */
    public function testGetCollection()
    {
        /** @var Mage_Webapi_Model_Resource_Acl_Role_Collection $collection */
        $collection = $this->getMockBuilder('Mage_Webapi_Model_Resource_Acl_Role_Collection')
            ->setConstructorArgs(array('resource' => $this->_roleResource))
            ->setMethods(array('_initSelect'))
            ->getMock();

        $collection->expects($this->any())
            ->method('_initSelect')
            ->withAnyParameters()
            ->will($this->returnValue(null));

        $model = $this->_createModel($this->_roleResource, $collection);
        $result = $model->getCollection();

        $this->assertAttributeEquals('Mage_Webapi_Model_Acl_Role', '_model', $result);
        $this->assertAttributeEquals('Mage_Webapi_Model_Resource_Acl_Role', '_resourceModel', $result);
    }
}
