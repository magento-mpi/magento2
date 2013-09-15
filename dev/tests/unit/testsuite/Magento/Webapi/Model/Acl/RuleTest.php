<?php
/**
 * Test class for \Magento\Webapi\Model\Acl\Rule
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webapi_Model_Acl_RuleTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_TestFramework_Helper_ObjectManager
     */
    protected $_helper;

    /**
     * @var \Magento\ObjectManager|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_objectManager;

    /**
     * @var \Magento\Webapi\Model\Resource\Acl\Rule|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_ruleResource;

    protected function setUp()
    {
        $this->_helper = new Magento_TestFramework_Helper_ObjectManager($this);

        $this->_objectManager = $this->getMockBuilder('Magento\ObjectManager')
            ->disableOriginalConstructor()
            ->setMethods(array('create'))
            ->getMockForAbstractClass();

        $this->_ruleResource = $this->getMockBuilder('Magento\Webapi\Model\Resource\Acl\Rule')
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
            ->will($this->returnValue($this->getMock('Magento\DB\Adapter\Pdo\Mysql', array(), array(), '', false)));
    }

    /**
     * Create Rule model.
     *
     * @param \Magento\Webapi\Model\Resource\Acl\Rule|PHPUnit_Framework_MockObject_MockObject $ruleResource
     * @param \Magento\Webapi\Model\Resource\Acl\User\Collection $resourceCollection
     * @return \Magento\Webapi\Model\Acl\Rule
     */
    protected function _createModel($ruleResource, $resourceCollection = null)
    {
        return $this->_helper->getObject('Magento\Webapi\Model\Acl\Rule', array(
            'eventDispatcher' => $this->getMock('Magento\Core\Model\Event\Manager', array(), array(), '', false),
            'cacheManager' => $this->getMock('Magento\Core\Model\CacheInterface', array(), array(), '', false),
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

        $this->assertAttributeEquals('Magento\Webapi\Model\Resource\Acl\Rule', '_resourceName', $model);
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
        $this->assertInstanceOf('Magento\Webapi\Model\Acl\Rule', $result);
    }

    /**
     * Test GET collection and _construct
     */
    public function testGetCollection()
    {
        $eventManager = $this->getMock('Magento\Core\Model\Event\Manager', array(), array(), '', false);
        $fetchStrategy = $this->getMockForAbstractClass('Magento\Data\Collection\Db\FetchStrategyInterface');

        /** @var PHPUnit_Framework_MockObject_MockObject $collection */
        $collection = $this->getMock(
            'Magento\Webapi\Model\Resource\Acl\Rule\Collection',
            array('_initSelect', 'setModel', 'getSelect'),
            array($eventManager, $fetchStrategy, $this->_ruleResource)
        );
        $collection->expects($this->any())->method('setModel')->with('Magento\Webapi\Model\Resource\Acl\Role');
        $collection->expects($this->any())
            ->method('getSelect')
            ->withAnyParameters()
            ->will($this->returnValue($this->getMock('Magento\DB\Select', array(), array(), '', false)));

        $model = $this->_createModel($this->_ruleResource, $collection);

        // Test _construct
        $result = $model->getCollection();

        $this->assertAttributeEquals('Magento\Webapi\Model\Resource\Acl\Rule', '_resourceModel', $result);

        // Test getByRole
        $resultColl = $result->getByRole(1);
        $this->assertInstanceOf('Magento\Webapi\Model\Resource\Acl\Rule\Collection', $resultColl);
    }
}
