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
        $contexts = [
            [
                'sortOrder' => 10,
                'type' => $userContextMock
            ]
        ];
        $model = new CompositeUserContext($contexts);
        $this->verifyUserContextIsAdded($model, $userContextMock);
    }

    public function testGetUserId()
    {
        $expectedUserId = 1;
        $expectedUserType = 'Customer';
        $userContextMock = $this->getMockBuilder('Magento\Authorization\Model\CompositeUserContext')
            ->disableOriginalConstructor()->setMethods(['getUserId', 'getUserType'])->getMock();
        $userContextMock->expects($this->any())->method('getUserId')->will($this->returnValue($expectedUserId));
        $userContextMock->expects($this->any())->method('getUserType')->will($this->returnValue($expectedUserType));
        $contexts = [
            [
                'sortOrder' => 10,
                'type' => $userContextMock
            ]
        ];
        $this->_model = new CompositeUserContext($contexts);
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
        $contexts = [
            [
                'sortOrder' => 10,
                'type' => $userContextMock
            ]
        ];
        $this->_model = new CompositeUserContext($contexts);
        $actualUserType = $this->_model->getUserType();
        $this->assertEquals($expectedUserType, $actualUserType, 'User Type is defined incorrectly.');
    }

    public function testUserContextCaching()
    {
        $expectedUserId = 1;
        $expectedUserType = 'Customer';
        $userContextMock = $this->getMockBuilder('Magento\Authorization\Model\CompositeUserContext')
            ->disableOriginalConstructor()->setMethods(['getUserId', 'getUserType'])->getMock();
        $userContextMock->expects($this->exactly(3))->method('getUserType')
            ->will($this->returnValue($expectedUserType));
        $userContextMock->expects($this->exactly(3))->method('getUserId')
            ->will($this->returnValue($expectedUserId));
        $contexts = [
            [
                'sortOrder' => 10,
                'type' => $userContextMock
            ]
        ];
        $this->_model = new CompositeUserContext($contexts);
        $this->_model->getUserId();
        $this->_model->getUserId();
        $this->_model->getUserType();
        $this->_model->getUserType();
    }

    public function testEmptyUserContext()
    {
        $expectedUserId = null;
        $userContextMock = $this->getMockBuilder('Magento\Authorization\Model\CompositeUserContext')
            ->disableOriginalConstructor()->setMethods(['getUserId'])->getMock();
        $userContextMock->expects($this->any())->method('getUserId')
            ->will($this->returnValue($expectedUserId));
        $contexts = [
            [
                'sortOrder' => 10,
                'type' => $userContextMock
            ]
        ];
        $this->_model = new CompositeUserContext($contexts);
        $actualUserId = $this->_model->getUserId();
        $this->assertEquals($expectedUserId, $actualUserId, 'User ID is defined incorrectly.');
    }

    public function testUserContextOrder()
    {
        $expectedUserId = null;
        $userContextMock1 = $this->getMockBuilder('Magento\Authorization\Model\CompositeUserContext')
            ->disableOriginalConstructor()->setMethods(['getUserType'])->getMock();
        $userContextMock1->expects($this->once())->method('getUserType')->will($this->returnValue(null));

        $userContextMock2 = $this->getMockBuilder('Magento\Authorization\Model\CompositeUserContext')
            ->disableOriginalConstructor()->setMethods(['getUserType'])->getMock();
        $userContextMock2->expects($this->once())->method('getUserType')->will($this->returnValue(null));

        $expectedUserType = 'Customer';
        $expectedUserId = 1234;
        $userContextMock3 = $this->getMockBuilder('Magento\Authorization\Model\CompositeUserContext')
            ->disableOriginalConstructor()->setMethods(['getUserType', 'getUserId'])->getMock();
        $userContextMock3->expects($this->once())->method('getUserType')->will($this->returnValue($expectedUserType));
        $userContextMock3->expects($this->exactly(2))->method('getUserId')->will($this->returnValue($expectedUserId));

        $contexts = [
            [
                'sortOrder' => 20,
                'type' => $userContextMock1
            ],
            [
                'sortOrder' => 30,
                'type' => $userContextMock3,
            ],
            [
                'sortOrder' => 10,
                'type' => $userContextMock2
            ],
        ];
        $this->_model = new CompositeUserContext($contexts);
        $actualUserId = $this->_model->getUserId();
        $this->assertEquals($expectedUserId, $actualUserId, 'User ID is defined incorrectly.');
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
