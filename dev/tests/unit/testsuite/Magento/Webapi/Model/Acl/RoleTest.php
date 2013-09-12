<?php
/**
 * Test class for Magento_Webapi_Model_Acl_User
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webapi_Model_Acl_RoleTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_TestFramework_Helper_ObjectManager
     */
    protected $_helper;

    /**
     * @var Magento_ObjectManager|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_objectManager;

    /**
     * @var Magento_Webapi_Model_Resource_Acl_Role|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_roleService;

    protected function setUp()
    {
        $this->_helper = new Magento_TestFramework_Helper_ObjectManager($this);

        $this->_objectManager = $this->getMockBuilder('Magento_ObjectManager')
            ->disableOriginalConstructor()
            ->setMethods(array('create'))
            ->getMockForAbstractClass();

        $this->_roleService = $this->getMockBuilder('Magento_Webapi_Model_Resource_Acl_Role')
            ->disableOriginalConstructor()
            ->setMethods(array('getIdFieldName', 'getReadConnection'))
            ->getMock();

        $this->_roleService->expects($this->any())
            ->method('getIdFieldName')
            ->withAnyParameters()
            ->will($this->returnValue('id'));

        $this->_roleService->expects($this->any())
            ->method('getReadConnection')
            ->withAnyParameters()
            ->will($this->returnValue($this->getMock('Magento_DB_Adapter_Pdo_Mysql', array(), array(), '', false)));
    }

    /**
     * Create Role model.
     *
     * @param Magento_Webapi_Model_Resource_Acl_Role $roleService
     * @param Magento_Webapi_Model_Resource_Acl_Role_Collection $serviceCollection
     * @return Magento_Webapi_Model_Acl_Role
     */
    protected function _createModel($roleService, $serviceCollection = null)
    {
        return $this->_helper->getObject('Magento_Webapi_Model_Acl_Role', array(
            'eventDispatcher' => $this->getMock('Magento_Core_Model_Event_Manager', array(), array(), '', false),
            'cacheManager' => $this->getMock('Magento_Core_Model_CacheInterface', array(), array(), '', false),
            'resource' => $roleService,
            'resourceCollection' => $serviceCollection
        ));
    }

    /**
     * Test constructor.
     */
    public function testConstructor()
    {
        $model = $this->_createModel($this->_roleService);

        $this->assertAttributeEquals('Magento_Webapi_Model_Resource_Acl_Role', '_resourceName', $model);
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
            'Magento_Webapi_Model_Resource_Acl_Role_Collection',
            array('_initSelect', 'setModel'),
            array($fetchStrategy, $this->_roleService)
        );

        $collection->expects($this->any())->method('setModel')->with('Magento_Webapi_Model_Resource_Acl_Role');

        $model = $this->_createModel($this->_roleService, $collection);
        $result = $model->getCollection();

        $this->assertAttributeEquals('Magento_Webapi_Model_Resource_Acl_Role', '_resourceModel', $result);
    }
}
