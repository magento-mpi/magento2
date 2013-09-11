<?php
/**
 * Test class for \Magento\Webapi\Model\Acl\User
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webapi_Model_Acl_UserTest extends PHPUnit_Framework_TestCase
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
     * @var \Magento\Webapi\Model\Resource\Acl\User|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_userResource;

    protected function setUp()
    {
        $this->_helper = new Magento_TestFramework_Helper_ObjectManager($this);

        $this->_objectManager = $this->getMockBuilder('Magento\ObjectManager')
            ->disableOriginalConstructor()
            ->setMethods(array('create'))
            ->getMockForAbstractClass();

        $this->_userResource = $this->getMockBuilder('Magento\Webapi\Model\Resource\Acl\User')
            ->disableOriginalConstructor()
            ->setMethods(array('getIdFieldName', 'getRoleUsers', 'load', 'getReadConnection'))
            ->getMock();

        $this->_userResource->expects($this->any())
            ->method('getIdFieldName')
            ->withAnyParameters()
            ->will($this->returnValue('id'));

        $this->_userResource->expects($this->any())
            ->method('getReadConnection')
            ->withAnyParameters()
            ->will($this->returnValue($this->getMock('Magento\DB\Adapter\Pdo\Mysql', array(), array(), '', false)));
    }

    /**
     * Create User model.
     *
     * @param \Magento\Webapi\Model\Resource\Acl\User $userResource
     * @param \Magento\Webapi\Model\Resource\Acl\User\Collection $resourceCollection
     * @return \Magento\Webapi\Model\Acl\User
     */
    protected function _createModel($userResource, $resourceCollection = null)
    {
        return $this->_helper->getObject('\Magento\Webapi\Model\Acl\User', array(
            'eventDispatcher' => $this->getMock('Magento\Core\Model\Event\Manager', array(), array(), '', false),
            'cacheManager' => $this->getMock('Magento\Core\Model\CacheInterface', array(), array(), '', false),
            'resource' => $userResource,
            'resourceCollection' => $resourceCollection
        ));
    }

    /**
     * Test constructor.
     */
    public function testConstructor()
    {
        $model = $this->_createModel($this->_userResource);

        $this->assertAttributeEquals('\Magento\Webapi\Model\Resource\Acl\User', '_resourceName', $model);
        $this->assertAttributeEquals('id', '_idFieldName', $model);
    }

    /**
     * Test getRoleUsers() method.
     */
    public function testGetRoleUsers()
    {
        $this->_userResource->expects($this->once())
            ->method('getRoleUsers')
            ->with(1)
            ->will($this->returnValue(array(1)));

        $model = $this->_createModel($this->_userResource);

        $result = $model->getRoleUsers(1);

        $this->assertEquals(array(1), $result);
    }

    /**
     * Test loadByKey() method.
     */
    public function testLoadByKey()
    {
        $this->_userResource->expects($this->once())
            ->method('load')
            ->with($this->anything(), 'key', 'api_key')
            ->will($this->returnSelf());

        $model = $this->_createModel($this->_userResource);

        $result = $model->loadByKey('key');
        $this->assertInstanceOf('\Magento\Webapi\Model\Acl\User', $result);
    }

    /**
     * Test public getters.
     */
    public function testPublicGetters()
    {
        $model = $this->_createModel($this->_userResource);

        $model->setData('secret', 'secretKey');

        $this->assertEquals('secretKey', $model->getSecret());
        $this->assertEquals('', $model->getCallBackUrl());
    }

    /**
     * Test GET collection and _construct
     */
    public function testGetCollection()
    {
        $fetchStrategy = $this->getMockForAbstractClass('Magento\Data\Collection\Db\FetchStrategyInterface');

        /** @var PHPUnit_Framework_MockObject_MockObject $collection */
        $collection = $this->getMock(
            '\Magento\Webapi\Model\Resource\Acl\User\Collection',
            array('_initSelect', 'setModel'),
            array($fetchStrategy, $this->_userResource)
        );

        $collection->expects($this->any())->method('setModel')->with('Magento\Webapi\Model\Acl\User');

        $model = $this->_createModel($this->_userResource, $collection);
        $result = $model->getCollection();

        $this->assertAttributeEquals('\Magento\Webapi\Model\Resource\Acl\User', '_resourceModel', $result);
    }
}
