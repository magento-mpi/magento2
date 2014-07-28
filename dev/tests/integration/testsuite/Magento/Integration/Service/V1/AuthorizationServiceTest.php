<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Integration\Service\V1;

use Magento\Integration\Service\V1\AuthorizationServiceTest\UserLocatorStub;
use Magento\Authz\Model\UserIdentifier;

/**
 * Integration authorization service test.
 */
class AuthorizationServiceTest extends \PHPUnit_Framework_TestCase
{
    /** @var AuthorizationService */
    protected $_service;

    protected function setUp()
    {
        $this->markTestIncomplete('Should be fixed/removed in scope of MAGETWO-26368');
        parent::setUp();
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $loggerMock = $this->getMockBuilder('Magento\\Framework\\Logger')->disableOriginalConstructor()->getMock();
        $loggerMock->expects($this->any())->method('logException')->will($this->returnSelf());
        $this->_service = $objectManager->create(
            'Magento\Integration\Service\V1\AuthorizationService',
            array(
                'userIdentifier' => $this->_createUserIdentifier(UserIdentifier::USER_TYPE_INTEGRATION),
                'logger' => $loggerMock
            )
        );
    }

    /**
     * @param string $userType
     * @param string[] $resources
     * @magentoDbIsolation enabled
     * @dataProvider basicAuthFlowProvider
     */
    public function testBasicAuthFlow($userType, $resources)
    {
        $userIdentifier = $this->_createUserIdentifier($userType);

        /** Preconditions check */
        $this->_ensurePermissionsAreNotGranted($userIdentifier, $resources);

        $this->_service->grantPermissions($userIdentifier->getUserId(), $resources);

        /** Validate that access to the specified resources is granted */
        $this->_ensurePermissionsAreGranted($userIdentifier, $resources);
    }

    public function basicAuthFlowProvider()
    {
        return array(
            'integration' => array(
                'userType' => UserIdentifier::USER_TYPE_INTEGRATION,
                'resources' => array('Magento_Sales::create', 'Magento_Cms::page', 'Magento_Adminhtml::dashboard')
            )
        );
    }

    /**
     * @param string $userType
     * @param string[] $initialResources
     * @param string[] $newResources
     * @magentoDbIsolation enabled
     * @dataProvider changePermissionsProvider
     */
    public function testChangePermissions($userType, $initialResources, $newResources)
    {
        $userIdentifier = $this->_createUserIdentifier($userType);

        $this->_service->grantPermissions($userIdentifier->getUserId(), $initialResources);
        /** Preconditions check */
        $this->_ensurePermissionsAreGranted($userIdentifier, $initialResources);
        $this->_ensurePermissionsAreNotGranted($userIdentifier, $newResources);

        $this->_service->grantPermissions($userIdentifier->getUserId(), $newResources);

        /** Check the results of permissions change */
        $this->_ensurePermissionsAreGranted($userIdentifier, $newResources);
        $this->_ensurePermissionsAreNotGranted($userIdentifier, $initialResources);
    }

    public function changePermissionsProvider()
    {
        return array(
            'integration' => array(
                'userType' => UserIdentifier::USER_TYPE_INTEGRATION,
                'initialResources' => array('Magento_Cms::page', 'Magento_Adminhtml::dashboard'),
                'newResources' => array('Magento_Sales::cancel', 'Magento_Cms::page_delete')
            ),
            'integration clear permissions' => array(
                'userType' => UserIdentifier::USER_TYPE_INTEGRATION,
                'initialResources' => array('Magento_Sales::capture', 'Magento_Cms::page_delete'),
                'newResources' => array()
            )
        );
    }

    /**
     * @magentoDbIsolation enabled
     */
    public function testGrantAllPermissions()
    {
        $userIdentifier = $this->_createUserIdentifier(UserIdentifier::USER_TYPE_INTEGRATION);
        $this->_service->grantAllPermissions($userIdentifier->getUserId());
        $this->_ensurePermissionsAreGranted($userIdentifier, array('Magento_Adminhtml::all'));
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

    /**
     * Check if access to the specified resources is prohibited to the user.
     *
     * @param UserIdentifier $userIdentifier
     * @param string[] $resources
     */
    protected function _ensurePermissionsAreNotGranted($userIdentifier, $resources)
    {
        foreach ($resources as $resource) {
            $this->assertFalse(
                $this->_service->isAllowed($resource, $userIdentifier),
                "Access to resource '{$resource}' is expected to be prohibited."
            );
        }
    }
}
