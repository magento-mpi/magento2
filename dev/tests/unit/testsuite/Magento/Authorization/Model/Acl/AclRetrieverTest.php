<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Authorization\Model\Acl;

use Magento\Authz\Model\UserIdentifier;
use Magento\Authorization\Model\Role;

class AclRetrieverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AclRetriever
     */
    protected $aclRetriever;

    /** @var \PHPUnit_Framework_MockObject_MockObject|Role $roleMock */
    protected $roleMock;

    protected function setup()
    {
        $this->aclRetriever = $this->createAclRetriever();
    }

    public function testGetAllowedResourcesByUserTypeGuest()
    {
        $expectedResources = ['anonymous'];
        /** @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Authz\Model\UserIdentifier $userIdentifierMock */
        $userIdentifierMock = $this->getMockBuilder('Magento\Authz\Model\UserIdentifier')
            ->disableOriginalConstructor()->setMethods(['getUserType'])->getMock();
        $userIdentifierMock->expects($this->any())->method('getUserType')->will($this->returnValue(UserIdentifier::USER_TYPE_GUEST));
        $allowedResources = $this->aclRetriever->getAllowedResourcesByUser($userIdentifierMock);
        $this->assertEquals($expectedResources, $allowedResources, 'Allowed resources for guests should be \'anonymous\'.');

    }

    public function testGetAllowedResourcesByUserTypeCustomer()
    {
        $expectedResources = ['self'];
        /** @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Authz\Model\UserIdentifier $userIdentifierMock */
        $userIdentifierMock = $this->getMockBuilder('Magento\Authz\Model\UserIdentifier')
            ->disableOriginalConstructor()->setMethods(['getUserType'])->getMock();
        $userIdentifierMock->expects($this->any())->method('getUserType')->will($this->returnValue(UserIdentifier::USER_TYPE_CUSTOMER));
        $allowedResources = $this->aclRetriever->getAllowedResourcesByUser($userIdentifierMock);
        $this->assertEquals($expectedResources, $allowedResources, 'Allowed resources for customers should be \'self\'.');

    }

    /**
     * @expectedException \Magento\Framework\Exception\AuthorizationException
     * @expectedExceptionMessage The role associated with the specified user cannot be found.
     */
    public function testGetAllowedResourcesByUserRoleNotFound()
    {
        $this->roleMock->expects($this->once())->method('getId')->will($this->returnValue(null));
        /** @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Authz\Model\UserIdentifier $userIdentifierMock */
        $userIdentifierMock = $this->getMockBuilder('Magento\Authz\Model\UserIdentifier')
            ->disableOriginalConstructor()->setMethods(['getUserType'])->getMock();
        $userIdentifierMock->expects($this->any())->method('getUserType')->will($this->returnValue(UserIdentifier::USER_TYPE_INTEGRATION));
        $this->aclRetriever->getAllowedResourcesByUser($userIdentifierMock);
    }

    public function testGetAllowedResourcesByUser()
    {
        $this->roleMock->expects($this->any())->method('getId')->will($this->returnValue(1));
        /** @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Authz\Model\UserIdentifier $userIdentifierMock */
        $userIdentifierMock = $this->getMockBuilder('Magento\Authz\Model\UserIdentifier')
            ->disableOriginalConstructor()->setMethods(['getUserType', 'getUserId'])->getMock();
        $userIdentifierMock->expects($this->any())->method('getUserType')->will($this->returnValue(UserIdentifier::USER_TYPE_INTEGRATION));
        $userIdentifierMock->expects($this->any())->method('getUserId')->will($this->returnValue(1));
        $expectedResources = ['Magento_Adminhtml::dashboard', 'Magento_Cms::page'];
        $this->assertEquals($expectedResources, $this->aclRetriever->getAllowedResourcesByUser($userIdentifierMock));
    }

    protected function createAclRetriever()
    {
        $this->roleMock = $this->getMock('Magento\Authorization\Model\Role', array('getId', '__wakeup'), array(), '', false);

        /** @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Authorization\Model\RoleFactory $roleFactoryMock */
        $roleFactoryMock = $this->getMock('Magento\Authorization\Model\RoleFactory', array('create'), array(), '', false);
        $roleFactoryMock->expects($this->any())->method('create')->will($this->returnValue($this->roleMock));

        /** @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Authorization\Model\Resource\Role\Collection $roleCollectionMock */
        $roleCollectionMock = $this->getMock('Magento\Authorization\Model\Resource\Role\Collection', array('setUserFilter', 'getFirstItem'), array(), '', false);
        $roleCollectionMock->expects($this->any())->method('setUserFilter')->will($this->returnSelf());
        $roleCollectionMock->expects($this->any())->method('getFirstItem')->will($this->returnValue($this->roleMock));

        /** @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Authorization\Model\Resource\Role\CollectionFactory $roleCollectionFactoryMock */
        $roleCollectionFactoryMock = $this->getMock('Magento\Authorization\Model\Resource\Role\CollectionFactory', array('create'), array(), '', false);
        $roleCollectionFactoryMock->expects($this->any())->method('create')->will($this->returnValue($roleCollectionMock));

        /** @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Authorization\Model\Rules $rulesMock1*/
        $rulesMock1 = $this->getMock('Magento\Authorization\Model\Rules', array('getResourceId', '__wakeup'), array(), '', false);
        $rulesMock1->expects($this->any())->method('getResourceId')->will($this->returnValue('Magento_Adminhtml::dashboard'));
        /** @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Authorization\Model\Rules $rulesMock1*/
        $rulesMock2 = $this->getMock('Magento\Authorization\Model\Rules', array('getResourceId', '__wakeup'), array(), '', false);
        $rulesMock2->expects($this->any())->method('getResourceId')->will($this->returnValue('Magento_Cms::page'));

        /** @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Authorization\Model\Resource\Rules\Collection $rulesCollectionMock */
        $rulesCollectionMock = $this->getMock('Magento\Authorization\Model\Resource\Rules\Collection', array('getByRoles', 'load', 'getItems'), array(), '', false);
        $rulesCollectionMock->expects($this->any())->method('getByRoles')->will($this->returnSelf());
        $rulesCollectionMock->expects($this->any())->method('load')->will($this->returnSelf());
        $rulesCollectionMock->expects($this->any())->method('getItems')->will($this->returnValue([$rulesMock1, $rulesMock2]));

        /** @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Authorization\Model\Resource\Rules\CollectionFactory $rulesCollectionFactoryMock */
        $rulesCollectionFactoryMock = $this->getMock('Magento\Authorization\Model\Resource\Rules\CollectionFactory', array('create'), array(), '', false);
        $rulesCollectionFactoryMock->expects($this->any())->method('create')->will($this->returnValue($rulesCollectionMock));

        /** @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Framework\Acl $aclMock */
        $aclMock = $this->getMock('Magento\Framework\Acl', array('has', 'isAllowed'), array(), '', false);
        $aclMock->expects($this->any())->method('has')->will($this->returnValue(true));
        $aclMock->expects($this->any())->method('isAllowed')->will($this->returnValue(true));

        /** @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Framework\Acl\Builder $aclBuilderMock */
        $aclBuilderMock = $this->getMock('Magento\Framework\Acl\Builder', array('getAcl'), array(), '', false);
        $aclBuilderMock->expects($this->any())->method('getAcl')->will($this->returnValue($aclMock));

        return new AclRetriever(
            $aclBuilderMock,
            $this->getMock('Magento\Authz\Model\UserIdentifier', array(), array(), '', false),
            $roleFactoryMock,
            $roleCollectionFactoryMock,
            $this->getMock('Magento\Authorization\Model\RulesFactory', array(), array(), '', false),
            $rulesCollectionFactoryMock,
            $this->getMock('Magento\Framework\Logger', array(), array(), '', false),
            $this->getMock('Magento\Framework\Acl\RootResource', array(), array(), '', false)
        );
    }
}
