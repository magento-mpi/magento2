<?php
/**
 * Test class for Mage_Webapi_Model_Acl_User
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
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
            ->will($this->returnValue($this->getMock('Magento_DB_Adapter_Pdo_Mysql', array(), array(), '', false)));
    }

    /**
     * Create Role model.
     *
     * @param Mage_Webapi_Model_Resource_Acl_Role $roleResource
     * @param Mage_Webapi_Model_Resource_Acl_Role_Collection $resourceCollection
     * @return Mage_Webapi_Model_Acl_Role
     */
    protected function _createModel($roleResource, $resourceCollection = null)
    {
        return $this->_helper->getObject('Mage_Webapi_Model_Acl_Role', array(
            'eventDispatcher' => $this->getMock('Magento_Core_Model_Event_Manager', array(), array(), '', false),
            'cacheManager' => $this->getMock('Magento_Core_Model_CacheInterface', array(), array(), '', false),
            'resource' => $roleResource,
            'resourceCollection' => $resourceCollection
        ));
    }

    /**
     * Test constructor.
     */
    public function testConstructor()
    {
        $model = $this->_createModel($this->_roleResource);

        $this->assertAttributeEquals('Mage_Webapi_Model_Resource_Acl_Role', '_resourceName', $model);
        $this->assertAttributeEquals('id', '_idFieldName', $model);
    }

    /**
     * Test GET collection and _construct
     */
    public function testGetCollection()
    {
        $fetchStrategy = $this->getMockForAbstractClass('Magento_Data_Collection_Db_FetchStrategyInterface');

        /** @var PHPUnit_Framework_MockObject_MockObject $collection */
        $collection = $this->getMock(
            'Mage_Webapi_Model_Resource_Acl_Role_Collection',
            array('_initSelect', 'setModel'),
            array($fetchStrategy, $this->_roleResource)
        );

        $collection->expects($this->any())->method('setModel')->with('Mage_Webapi_Model_Resource_Acl_Role');

        $model = $this->_createModel($this->_roleResource, $collection);
        $result = $model->getCollection();

        $this->assertAttributeEquals('Mage_Webapi_Model_Resource_Acl_Role', '_resourceModel', $result);
    }
}
