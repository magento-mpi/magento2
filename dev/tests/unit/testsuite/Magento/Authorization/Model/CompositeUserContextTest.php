<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Authorization\Model;

class CompositeUserContextTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var CompositeUserContext
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model = new CompositeUserContext();
    }

    public function testConstructor()
    {
        $userContextMock = $this->createUserContextMock();
        $model = new CompositeUserContext([$userContextMock]);
        $this->verifyUserContextIsAdded($model, $userContextMock);
    }

    public function testGetUserId()
    {
        $expectedUserId = 1;
        $userContextMock = $this->getMockBuilder('Magento\Authorization\Model\CompositeUserContext')
            ->disableOriginalConstructor()->setMethods(['getUserId'])->getMock();
        $userContextMock->expects($this->any())->method('getUserId')->will($this->returnValue($expectedUserId));
        $this->_model = new CompositeUserContext([$userContextMock]);
        $actualUserId = $this->_model->getUserId();
        $this->assertEquals($expectedUserId, $actualUserId, 'User ID is defined incorrectly.');
    }

    public function testGetUserType()
    {
        $expectedUserId = 1;
        $expectedUserType = 'Customer';
        $userContextMock = $this->getMockBuilder('Magento\Authorization\Model\CompositeUserContext')
            ->disableOriginalConstructor()->setMethods(['getUserId', 'getUserType'])->getMock();
        $userContextMock->expects($this->any())->method('getUserId')->will($this->returnValue($expectedUserId));
        $userContextMock->expects($this->any())->method('getUserType')->will($this->returnValue($expectedUserType));
        $this->_model = new CompositeUserContext([$userContextMock]);
        $actualUserType = $this->_model->getUserType();
        $this->assertEquals($expectedUserType, $actualUserType, 'User Type is defined incorrectly.');
    }

    public function testUserContextCaching()
    {
        $expectedUserId = 1;
        $userContextMock = $this->getMockBuilder('Magento\Authorization\Model\CompositeUserContext')
            ->disableOriginalConstructor()->setMethods(['getUserId'])->getMock();
        $userContextMock->expects($this->exactly(3))->method('getUserId')
            ->will($this->returnValue($expectedUserId));
        $this->_model = new CompositeUserContext([$userContextMock]);
        $this->_model->getUserId();
        $this->_model->getUserId();
    }

    public function testEmptyUserContext()
    {
        $expectedUserId = null;
        $userContextMock = $this->getMockBuilder('Magento\Authorization\Model\CompositeUserContext')
            ->disableOriginalConstructor()->setMethods(['getUserId'])->getMock();
        $userContextMock->expects($this->any())->method('getUserId')
            ->will($this->returnValue($expectedUserId));
        $this->_model = new CompositeUserContext([$userContextMock]);
        $actualUserType = $this->_model->getUserId();
        $this->assertEquals($expectedUserId, $actualUserType, 'User ID is defined incorrectly.');
    }

    /**
     * @param int|null $userId
     * @param string|null $userType
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function createUserContextMock($userId = null, $userType = null)
    {
        $useContextMock = $this->getMockBuilder('Magento\Authorization\Model\CompositeUserContext')
            ->disableOriginalConstructor()->setMethods(['getUserId', 'getUserType'])->getMock();
        if (!is_null($userId) && !is_null($userType)) {
            $useContextMock->expects($this->any())->method('getUserId')->will($this->returnValue($userId));
            $useContextMock->expects($this->any())->method('getUserType')->will($this->returnValue($userType));
        }
        return $useContextMock;
    }

    /**
     * @param CompositeUserContext $model
     * @param CompositeUserContext $userContextMock
     */
    protected function verifyUserContextIsAdded($model, $userContextMock)
    {
        $userContext = new \ReflectionProperty(
            'Magento\Authorization\Model\CompositeUserContext',
            'userContexts'
        );
        $userContext->setAccessible(true);
        $values = $userContext->getValue($model);
        $this->assertCount(1, $values, 'User context is not registered.');
        $this->assertEquals($userContextMock, $values[0], 'User context is registered incorrectly.');
    }
}
