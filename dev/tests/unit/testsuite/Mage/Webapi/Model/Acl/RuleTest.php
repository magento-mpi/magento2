<?php
/**
 * Test class for Mage_Webapi_Model_Acl_Rule
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Webapi_Model_Acl_RuleTest extends PHPUnit_Framework_TestCase
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
     * @var Mage_Webapi_Model_Service_Acl_Rule|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_ruleService;

    protected function setUp()
    {
        $this->_helper = new Magento_Test_Helper_ObjectManager($this);

        $this->_objectManager = $this->getMockBuilder('Magento_ObjectManager')
            ->disableOriginalConstructor()
            ->setMethods(array('create'))
            ->getMockForAbstractClass();

        $this->_ruleService = $this->getMockBuilder('Mage_Webapi_Model_Service_Acl_Rule')
            ->disableOriginalConstructor()
            ->setMethods(array('saveServices', 'getIdFieldName', 'getReadConnection'))
            ->getMock();

        $this->_ruleService->expects($this->any())
            ->method('getIdFieldName')
            ->withAnyParameters()
            ->will($this->returnValue('id'));

        $this->_ruleService->expects($this->any())
            ->method('getReadConnection')
            ->withAnyParameters()
            ->will($this->returnValue($this->getMock('Varien_Db_Adapter_Pdo_Mysql', array(), array(), '', false)));
    }

    /**
     * Create Rule model.
     *
     * @param Mage_Webapi_Model_Service_Acl_Rule|PHPUnit_Framework_MockObject_MockObject $ruleService
     * @param Mage_Webapi_Model_Service_Acl_User_Collection $serviceCollection
     * @return Mage_Webapi_Model_Acl_Rule
     */
    protected function _createModel($ruleService, $serviceCollection = null)
    {
        return $this->_helper->getObject('Mage_Webapi_Model_Acl_Rule', array(
            'eventDispatcher' => $this->getMock('Mage_Core_Model_Event_Manager', array(), array(), '', false),
            'cacheManager' => $this->getMock('Mage_Core_Model_CacheInterface', array(), array(), '', false),
            'resource' => $ruleService,
            'resourceCollection' => $serviceCollection
        ));
    }

    /**
     * Test constructor.
     */
    public function testConstructor()
    {
        $model = $this->_createModel($this->_ruleService);

        $this->assertAttributeEquals('Mage_Webapi_Model_Service_Acl_Rule', '_resourceName', $model);
        $this->assertAttributeEquals('id', '_idFieldName', $model);
    }

    /**
     * Test getRoleUsers() method.
     */
    public function testGetRoleUsers()
    {
        $this->_ruleService->expects($this->once())
            ->method('saveServices')
            ->withAnyParameters()
            ->will($this->returnSelf());

        $model = $this->_createModel($this->_ruleService);
        $result = $model->saveServices();
        $this->assertInstanceOf('Mage_Webapi_Model_Acl_Rule', $result);
    }

    /**
     * Test GET collection and _construct
     */
    public function testGetCollection()
    {
        $fetchStrategy = $this->getMockForAbstractClass('Varien_Data_Collection_Db_FetchStrategyInterface');

        /** @var PHPUnit_Framework_MockObject_MockObject $collection */
        $collection = $this->getMock(
            'Mage_Webapi_Model_Service_Acl_Rule_Collection',
            array('_initSelect', 'setModel', 'getSelect'),
            array($fetchStrategy, $this->_ruleService)
        );
        $collection->expects($this->any())->method('setModel')->with('Mage_Webapi_Model_Service_Acl_Role');
        $collection->expects($this->any())
            ->method('getSelect')
            ->withAnyParameters()
            ->will($this->returnValue($this->getMock('Varien_Db_Select', array(), array(), '', false)));

        $model = $this->_createModel($this->_ruleService, $collection);

        // Test _construct
        $result = $model->getCollection();

        $this->assertAttributeEquals('Mage_Webapi_Model_Service_Acl_Rule', '_resourceModel', $result);

        // Test getByRole
        $resultColl = $result->getByRole(1);
        $this->assertInstanceOf('Mage_Webapi_Model_Service_Acl_Rule_Collection', $resultColl);
    }
}
