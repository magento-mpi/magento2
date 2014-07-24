<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Authz\Service;

use Magento\Authz\Model\UserIdentifier;
use Magento\Authorization\Model\Role;

class AuthorizationV1Test extends \PHPUnit_Framework_TestCase
{
    /** @var AuthorizationV1 */
    protected $_authzService;

    protected function setUp()
    {
        /** @var \PHPUnit_Framework_MockObject_MockObject|Role $roleMock */
        $roleMock = $this->getMock(
            'Magento\Authorization\Model\Role',
            array('load', 'delete', '__wakeup'),
            array(),
            '',
            false
        );
        $roleMock->expects($this->any())->method('load')->will($this->returnSelf());
        $roleMock->expects($this->any())->method('delete')->will($this->returnSelf());

        /** @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Authorization\Model\RoleFactory $roleFactoryMock */
        $roleFactoryMock = $this->getMock(
            'Magento\Authorization\Model\RoleFactory',
            array('create'),
            array(),
            '',
            false
        );
        $roleFactoryMock->expects($this->any())->method('create')->will($this->returnValue($roleMock));

        $this->_authzService = new AuthorizationV1(
            $this->getMock('Magento\Framework\Acl\Builder', array(), array(), '', false),
            $this->getMock('Magento\Authz\Model\UserIdentifier', array(), array(), '', false),
            $roleFactoryMock,
            $this->getMock('Magento\Authorization\Model\Resource\Role\CollectionFactory', array(), array(), '', false),
            $this->getMock('Magento\Authorization\Model\RulesFactory', array(), array(), '', false),
            $this->getMock('Magento\Authorization\Model\Resource\Rules\CollectionFactory', array(), array(), '', false),
            $this->getMock('Magento\Framework\Logger', array(), array(), '', false),
            $this->getMock('Magento\Framework\Acl\RootResource', array(), array(), '', false)
        );
    }

    public function testRemovePermissions()
    {
        $this->_authzService->removePermissions($this->_getUserIdentifierMock(UserIdentifier::USER_TYPE_INTEGRATION));
    }

    /**
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     * @expectedExceptionMessage No such entity with userType = 5
     */
    public function testRemovePermissionsException()
    {
        // Undefined user identifier type outside \Magento\Authz\Model\UserIdentifier::USER_TYPE_*
        $this->_authzService->removePermissions($this->_getUserIdentifierMock(5));
    }

    /**
     * @param string $getUserTypeValue
     * @return UserIdentifier|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function _getUserIdentifierMock($getUserTypeValue)
    {
        /** @var UserIdentifier|\PHPUnit_Framework_MockObject_MockObject  $userIdentiferMock */
        $userIdentiferMock = $this->getMock(
            'Magento\Authz\Model\UserIdentifier',
            array('getUserType', 'getUserId'),
            array(),
            '',
            false
        );

        $userIdentiferMock->expects($this->any())->method('getUserType')->will($this->returnValue($getUserTypeValue));

        return $userIdentiferMock;
    }
}
