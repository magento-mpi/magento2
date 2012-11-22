<?php
/**
 * Test class for Mage_Webapi_Model_Resource_Acl_Rule
 *
 * @copyright {}
 */
class Mage_Webapi_Model_Resource_Acl_RuleTest extends PHPUnit_Framework_TestCase
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

        $this->_adapter = $this->getMockBuilder('Varien_Db_Adapter_Pdo_Mysql')
            ->disableOriginalConstructor()
            ->setMethods(array('select', 'fetchCol', 'fetchAll', 'beginTransaction', 'commit', 'rollback', 'delete'))
            ->getMock();

        $this->_adapter->expects($this->any())
            ->method('select')
            ->withAnyParameters()
            ->will($this->returnValue(new Varien_Db_Select($this->getMock('Varien_Db_Adapter_Pdo_Mysql', array(), array(), '', false))));

        $this->_adapter->expects($this->any())
            ->method('fetchCol')
            ->withAnyParameters()
            ->will($this->returnArgument(0));

        $this->_adapter->expects($this->any())
            ->method('fetchAll')
            ->withAnyParameters()
            ->will($this->returnArgument(0));

        $this->_adapter->expects($this->any())
            ->method('beginTransaction')
            ->withAnyParameters()
            ->will($this->returnSelf());

        $this->_resource->expects($this->any())
            ->method('getConnection')
            ->withAnyParameters()
            ->will($this->returnValue($this->_adapter));
    }

    /**
     * Create resource model
     *
     * @return Mage_Webapi_Model_Resource_Acl_Rule
     */
    protected function _createModel()
    {
        return $this->_helper->getModel('Mage_Webapi_Model_Resource_Acl_Rule', array(
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

        $this->assertAttributeEquals('webapi_rule', '_mainTable', $model);
        $this->assertAttributeEquals('rule_id', '_idFieldName', $model);
    }

    /**
     * Test getRuleList()
     */
    public function testGetRuleList()
    {
        $model = $this->_createModel();

        /** @var Varien_Db_Select $select */
        $select = $model->getRuleList();
        $from = $select->getPart('from');

        $this->assertEquals(array('webapi_rule' =>
            array('joinCondition' => null,
                'joinType' => 'from',
                'schema' => null,
                'tableName' => 'webapi_rule',
            )), $from);
    }

    /**
     * Test getResourceIdsByRole()
     */
    public function testGetResourceIdsByRole()
    {
        $model = $this->_createModel();

        /** @var Varien_Db_Select $select */
        $select = $model->getResourceIdsByRole(1);
        $from = $select->getPart('from');

        $this->assertEquals(array('webapi_rule' =>
            array('joinCondition' => null,
                'joinType' => 'from',
                'schema' => null,
                'tableName' => 'webapi_rule',
            )), $from);
    }

    /**
     * Test saveResources()
     */
    public function testSaveResources()
    {
        $ruleResource = $this->getMockBuilder('Mage_Webapi_Model_Resource_Acl_Rule')
            ->disableOriginalConstructor()
            ->setMethods(array('saveResources', 'getIdFieldName', 'getReadConnection'))
            ->getMock();

        $ruleResource->expects($this->any())
            ->method('getIdFieldName')
            ->withAnyParameters()
            ->will($this->returnValue('id'));

        $ruleResource->expects($this->any())
            ->method('getReadConnection')
            ->withAnyParameters()
            ->will($this->returnValue($this->getMock('Varien_Db_Adapter_Pdo_Mysql', array(), array(), '', false)));

        /** @var Mage_Webapi_Model_Acl_Rule $rule */
        $rule = $this->_helper->getModel('Mage_Webapi_Model_Acl_Rule', array(
            'eventDispatcher' => $this->getMock('Mage_Core_Model_Event_Manager', array(), array(), '', false),
            'cacheManager' => $this->getMock('Mage_Core_Model_Cache', array(), array(), '', false),
            'resource' => $ruleResource
        ));

        $rule->setRoleId(1);

        $this->_adapter->expects($this->once())
            ->method('delete')
            ->withAnyParameters()
            ->will($this->returnValue(array()));

        $model = $this->_createModel();
        $result = $model->saveResources($rule);
    }
}
