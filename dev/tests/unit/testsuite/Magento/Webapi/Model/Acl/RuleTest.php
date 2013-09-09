<?php
/**
 * Test class for Magento_Webapi_Model_Acl_Rule
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webapi_Model_Acl_RuleTest extends PHPUnit_Framework_TestCase
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
     * @var Magento_Webapi_Model_Resource_Acl_Rule|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_ruleResource;

    protected function setUp()
    {
        $this->_helper = new Magento_Test_Helper_ObjectManager($this);

        $this->_objectManager = $this->getMockBuilder('Magento_ObjectManager')
            ->disableOriginalConstructor()
            ->setMethods(array('create'))
            ->getMockForAbstractClass();

        $this->_ruleResource = $this->getMockBuilder('Magento_Webapi_Model_Resource_Acl_Rule')
            ->disableOriginalConstructor()
            ->setMethods(array('saveResources', 'getIdFieldName', 'getReadConnection'))
            ->getMock();

        $this->_ruleResource->expects($this->any())
            ->method('getIdFieldName')
            ->withAnyParameters()
            ->will($this->returnValue('id'));

        $this->_ruleResource->expects($this->any())
            ->method('getReadConnection')
            ->withAnyParameters()
            ->will($this->returnValue($this->getMock('Magento_DB_Adapter_Pdo_Mysql', array(), array(), '', false)));
    }

    /**
     * Create Rule model.
     *
     * @param Magento_Webapi_Model_Resource_Acl_Rule|PHPUnit_Framework_MockObject_MockObject $ruleResource
     * @param Magento_Webapi_Model_Resource_Acl_User_Collection $resourceCollection
     * @return Magento_Webapi_Model_Acl_Rule
     */
    protected function _createModel($ruleResource, $resourceCollection = null)
    {
        return $this->_helper->getObject('Magento_Webapi_Model_Acl_Rule', array(
            'eventDispatcher' => $this->getMock('Magento_Core_Model_Event_Manager', array(), array(), '', false),
            'cacheManager' => $this->getMock('Magento_Core_Model_CacheInterface', array(), array(), '', false),
            'resource' => $ruleResource,
            'resourceCollection' => $resourceCollection
        ));
    }

    /**
     * Test constructor.
     */
    public function testConstructor()
    {
        $model = $this->_createModel($this->_ruleResource);

        $this->assertAttributeEquals('Magento_Webapi_Model_Resource_Acl_Rule', '_resourceName', $model);
        $this->assertAttributeEquals('id', '_idFieldName', $model);
    }

    /**
     * Test getRoleUsers() method.
     */
    public function testGetRoleUsers()
    {
        $this->_ruleResource->expects($this->once())
            ->method('saveResources')
            ->withAnyParameters()
            ->will($this->returnSelf());

        $model = $this->_createModel($this->_ruleResource);
        $result = $model->saveResources();
        $this->assertInstanceOf('Magento_Webapi_Model_Acl_Rule', $result);
    }

    /**
     * Test GET collection and _construct
     */
    public function testGetCollection()
    {
        $eventManager = $this->getMock('Magento_Core_Model_Event_Manager', array(), array(), '', false);
        $fetchStrategy = $this->getMockForAbstractClass('Magento_Data_Collection_Db_FetchStrategyInterface');

        /** @var PHPUnit_Framework_MockObject_MockObject $collection */
        $collection = $this->getMock(
            'Magento_Webapi_Model_Resource_Acl_Rule_Collection',
            array('_initSelect', 'setModel', 'getSelect'),
            array($eventManager, $fetchStrategy, $this->_ruleResource)
        );
        $collection->expects($this->any())->method('setModel')->with('Magento_Webapi_Model_Resource_Acl_Role');
        $collection->expects($this->any())
            ->method('getSelect')
            ->withAnyParameters()
            ->will($this->returnValue($this->getMock('Magento_DB_Select', array(), array(), '', false)));

        $model = $this->_createModel($this->_ruleResource, $collection);

        // Test _construct
        $result = $model->getCollection();

        $this->assertAttributeEquals('Magento_Webapi_Model_Resource_Acl_Rule', '_resourceModel', $result);

        // Test getByRole
        $resultColl = $result->getByRole(1);
        $this->assertInstanceOf('Magento_Webapi_Model_Resource_Acl_Rule_Collection', $resultColl);
    }
}
