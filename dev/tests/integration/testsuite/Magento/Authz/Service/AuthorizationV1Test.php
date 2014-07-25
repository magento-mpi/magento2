<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Authz\Service;

use Magento\Authz\Service\AuthorizationV1Test\UserLocatorStub;
use Magento\Authz\Model\UserIdentifier;
use Magento\Authorization\Model\Acl\AclRetriever;

/**
 * Authorization service test.
 */
class AuthorizationV1Test extends \PHPUnit_Framework_TestCase
{
    /** @var AuthorizationV1 */
    protected $_service;

    /** @var AclRetriever */
    protected $_aclRetriever;

    protected function setUp()
    {
        $this->markTestIncomplete('Should be fixed/removed in scope of MAGETWO-26342');
        parent::setUp();
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $loggerMock = $this->getMockBuilder('Magento\\Framework\\Logger')->disableOriginalConstructor()->getMock();
        $loggerMock->expects($this->any())->method('logException')->will($this->returnSelf());
        $this->_service = $objectManager->create(
            'Magento\\Authz\\Service\\AuthorizationV1',
            array(
                'userIdentifier' => $this->_createUserIdentifier(UserIdentifier::USER_TYPE_INTEGRATION),
                'logger' => $loggerMock
            )
        );
        $this->_aclRetriever = $objectManager->create('Magento\Authorization\Model\Acl\AclRetriever');
    }

    /**
     * @magentoDbIsolation enabled
     */
    public function testGetAllowedResources()
    {
        $userIdentifierA = $this->_createUserIdentifier(UserIdentifier::USER_TYPE_INTEGRATION);
        $resourcesA = array('Magento_Adminhtml::dashboard', 'Magento_Cms::page');
        $this->_service->grantPermissions($userIdentifierA, $resourcesA);

        $userIdentifierB = $this->_createUserIdentifier(UserIdentifier::USER_TYPE_INTEGRATION);
        $resourcesB = array('Magento_Cms::block', 'Magento_Sales::cancel');
        $this->_service->grantPermissions($userIdentifierB, $resourcesB);

        /** Preconditions check */
        $this->_ensurePermissionsAreGranted($userIdentifierA, $resourcesA);
        $this->_ensurePermissionsAreGranted($userIdentifierB, $resourcesB);

        $this->assertEquals(
            $resourcesA,
            $this->_aclRetriever->getAllowedResourcesByUser($userIdentifierA),
            "The list of resources allowed to the user is invalid."
        );

        $this->assertEquals(
            $resourcesB,
            $this->_aclRetriever->getAllowedResourcesByUser($userIdentifierB),
            "The list of resources allowed to the user is invalid."
        );
    }

    /**
     * @expectedException \Magento\Framework\Exception\AuthorizationException
     * @expectedMessage The role associated with the specified user cannot be found.
     */
    public function testGetAllowedResourcesRoleNotFound()
    {
        $userIdentifier = $this->_createUserIdentifier(UserIdentifier::USER_TYPE_INTEGRATION);
        $this->_aclRetriever->getAllowedResourcesByUser($userIdentifier);
    }

    /**
     * Create new User identifier
     *
     * @param string $userType
     * @return UserIdentifier
     */
    protected function _createUserIdentifier($userType)
    {
        $userId = $userType == UserIdentifier::USER_TYPE_GUEST ? 0 : rand(1, 1000);
        $userLocatorStub = new UserLocatorStub();
        return new UserIdentifier($userLocatorStub, $userType, $userId);
    }

    /**
     * Check if user has access to the specified resources.
     *
     * @param UserIdentifier $userIdentifier
     * @param string[] $resources
     */
    protected function _ensurePermissionsAreGranted($userIdentifier, $resources)
    {
        foreach ($resources as $resource) {
            $this->assertTrue(
                $this->_service->isAllowed($resource, $userIdentifier),
                "Access to resource '{$resource}' is prohibited while it is expected to be granted."
            );
        }
    }
}
