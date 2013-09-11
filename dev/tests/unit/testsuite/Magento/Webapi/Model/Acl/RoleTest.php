<?php
/**
 * Test class for \Magento\Webapi\Model\Acl\User
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
     * @var \Magento\ObjectManager|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_objectManager;

    /**
     * @var \Magento\Webapi\Model\Resource\Acl\Role|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_roleResource;

    protected function setUp()
    {
        $this->_helper = new Magento_TestFramework_Helper_ObjectManager($this);

        $this->_objectManager = $this->getMockBuilder('Magento\ObjectManager')
            ->disableOriginalConstructor()
            ->setMethods(array('create'))
            ->getMockForAbstractClass();

        $this->_roleResource = $this->getMockBuilder('Magento\Webapi\Model\Resource\Acl\Role')
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
            ->will($this->returnValue($this->getMock('Magento\DB\Adapter\Pdo\Mysql', array(), array(), '', false)));
    }

    /**
     * Create Role model.
     *
     * @param \Magento\Webapi\Model\Resource\Acl\Role $roleResource
     * @param \Magento\Webapi\Model\Resource\Acl\Role\Collection $resourceCollection
     * @return \Magento\Webapi\Model\Acl\Role
     */
    protected function _createModel($roleResource, $resourceCollection = null)
    {
        return $this->_helper->getObject('Magento\Webapi\Model\Acl\Role', array(
            'eventDispatcher' => $this->getMock('Magento\Core\Model\Event\Manager', array(), array(), '', false),
            'cacheManager' => $this->getMock('Magento\Core\Model\CacheInterface', array(), array(), '', false),
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

        $this->assertAttributeEquals('Magento\Webapi\Model\Resource\Acl\Role', '_resourceName', $model);
        $this->assertAttributeEquals('id', '_idFieldName', $model);
    }

    /**
     * Test GET collection and _construct
     */
    public function testGetCollection()
    {
        $fetchStrategy = $this->getMockForAbstractClass('Magento\Data\Collection\Db\FetchStrategyInterface');

        /** @var PHPUnit_Framework_MockObject_MockObject $collection */
        $collection = $this->getMock(
            'Magento\Webapi\Model\Resource\Acl\Role\Collection',
            array('_initSelect', 'setModel'),
            array($fetchStrategy, $this->_roleResource)
        );

        $collection->expects($this->any())->method('setModel')->with('Magento\Webapi\Model\Resource\Acl\Role');

        $model = $this->_createModel($this->_roleResource, $collection);
        $result = $model->getCollection();

        $this->assertAttributeEquals('Magento\Webapi\Model\Resource\Acl\Role', '_resourceModel', $result);
    }
}
