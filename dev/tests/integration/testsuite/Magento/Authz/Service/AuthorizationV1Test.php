<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Authz\Service;

use Magento\Authz\Model\UserContext;

/**
 * Authorization service test.
 */
class AuthorizationV1Test extends \PHPUnit_Framework_TestCase
{
    /** @var AuthorizationV1 */
    protected $_service;

    protected function setUp()
    {
        parent::setUp();
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $loggerMock = $this->getMockBuilder('Magento\\Logger')->disableOriginalConstructor()->getMock();
        $loggerMock->expects($this->any())->method('logException')->will($this->returnSelf());
        $this->_service = $objectManager->create(
            'Magento\\Authz\\Service\\AuthorizationV1',
            array('userContext' => $this->_createUserContext(UserContext::USER_TYPE_GUEST), 'logger' => $loggerMock)
        );
    }

    /**
     * @param UserContext $userContext
     * @param string[] $resources
     * @magentoDbIsolation enabled
     * @dataProvider basicAuthFlowProvider
     */
    public function testBasicAuthFlow($userContext, $resources)
    {
        if ($userContext->getUserType() == UserContext::USER_TYPE_ADMIN) {
            // TODO: Remove when services for admin roles are implemented
            $this->setExpectedException(
                '\Exception',
                'Error happened while granting permissions. Check exception log for details.'
            );
        }
        /** Preconditions check */
        $this->_ensurePermissionsAreNotGranted($userContext, $resources);

        $this->_service->grantPermissions($userContext, $resources);

        /** Validate that access to the specified resources is granted */
        $this->_ensurePermissionsAreGranted($userContext, $resources);
    }

    public function basicAuthFlowProvider()
    {
        return array(
            'integration' => array(
                'userContext' => $this->_createUserContext(UserContext::USER_TYPE_INTEGRATION),
                'resources' => array('Magento_Sales::invoice', 'Magento_Cms::page', 'Magento_Adminhtml::dashboard')
            ),
            'admin' => array(
                'userContext' => $this->_createUserContext(UserContext::USER_TYPE_ADMIN),
                'resources' => array('Magento_Sales::use', 'Magento_Cms::block')
            ),
            'guest' => array(
                'userContext' => $this->_createUserContext(UserContext::USER_TYPE_GUEST),
                'resources' => array('Magento_Sales::ship', 'Magento_Cms::save'),
            ),
            'customer' => array(
                'userContext' => $this->_createUserContext(UserContext::USER_TYPE_CUSTOMER),
                'resources' => array('Magento_Sales::hold', 'Magento_Cms::page_delete'),
            ),
        );
    }

    /**
     * @param UserContext $userContext
     * @param string[] $initialResources
     * @param string[] $newResources
     * @magentoDbIsolation enabled
     * @dataProvider changePermissionsProvider
     */
    public function testChangePermissions($userContext, $initialResources, $newResources)
    {
        if ($userContext->getUserType() == UserContext::USER_TYPE_ADMIN) {
            // TODO: Remove when services for admin roles are implemented
            $this->setExpectedException(
                '\Exception',
                'Error happened while granting permissions. Check exception log for details.'
            );
        }

        $this->_service->grantPermissions($userContext, $initialResources);
        /** Preconditions check */
        $this->_ensurePermissionsAreGranted($userContext, $initialResources);
        $this->_ensurePermissionsAreNotGranted($userContext, $newResources);

        $this->_service->grantPermissions($userContext, $newResources);

        /** Check the results of permissions change */
        $this->_ensurePermissionsAreGranted($userContext, $newResources);
        $this->_ensurePermissionsAreNotGranted($userContext, $initialResources);
    }

    public function changePermissionsProvider()
    {
        return array(
            'integration' => array(
                'userContext' => $this->_createUserContext(UserContext::USER_TYPE_INTEGRATION),
                'initialResources' => array('Magento_Cms::page', 'Magento_Adminhtml::dashboard'),
                'newResources' => array('Magento_Sales::hold', 'Magento_Cms::page_delete')
            ),
            'admin' => array(
                'userContext' => $this->_createUserContext(UserContext::USER_TYPE_ADMIN),
                'initialResources' => array('Magento_Sales::use', 'Magento_Cms::block'),
                'newResources' => array('Magento_Sales::hold')
            ),
            'guest' => array(
                'userContext' => $this->_createUserContext(UserContext::USER_TYPE_GUEST),
                'initialResources' => array('Magento_Sales::ship', 'Magento_Cms::save'),
                'newResources' => array('Magento_Sales::use', 'Magento_Cms::block'),
            ),
            'customer' => array(
                'userContext' => $this->_createUserContext(UserContext::USER_TYPE_CUSTOMER),
                'initialResources' => array('Magento_Sales::hold', 'Magento_Cms::page_delete'),
                'newResources' => array('Magento_Sales::ship', 'Magento_Cms::save'),
            ),
            'integration clear permissions' => array(
                'userContext' => $this->_createUserContext(UserContext::USER_TYPE_CUSTOMER),
                'initialResources' => array('Magento_Sales::hold', 'Magento_Cms::page_delete'),
                'newResources' => array(),
            ),
        );
    }

    public function testIsAllowedArrayOfResources()
    {
        $userContext = $this->_createUserContext(UserContext::USER_TYPE_INTEGRATION);
        $resources = array('Magento_Cms::page', 'Magento_Adminhtml::dashboard');
        $this->_service->grantPermissions($userContext, $resources);
        /** Preconditions check */
        $this->_ensurePermissionsAreGranted($userContext, $resources);

        /** Ensure that permissions check to multiple resources at once works as expected */
        $this->assertTrue(
            $this->_service->isAllowed($resources, $userContext),
            'Access to multiple resources is expected to be granted, but is prohibited.'
        );
        $this->assertFalse(
            $this->_service->isAllowed(array_merge($resources, array('invalid_resource')), $userContext),
            'Access is expected to be denied when at least one of the resources is unavailable.'
        );
    }

    /**
     * Create new User Context
     *
     * @param string $userType
     * @return UserContext
     */
    protected function _createUserContext($userType)
    {
        $userId = ($userType == UserContext::USER_TYPE_GUEST) ? 0 : rand(1, 1000);
        return new UserContext($userType, $userId);
    }

    /**
     * Check if user has access to the specified resources.
     *
     * @param UserContext $userContext
     * @param string[] $resources
     */
    protected function _ensurePermissionsAreGranted($userContext, $resources)
    {
        foreach ($resources as $resource) {
            $this->assertTrue(
                $this->_service->isAllowed($resource, $userContext),
                "Access to resource '{$resource}' is prohibited while it is expected to be granted."
            );
        }
    }

    /**
     * Check if access to the specified resources is prohibited to the user.
     *
     * @param UserContext $userContext
     * @param string[] $resources
     */
    protected function _ensurePermissionsAreNotGranted($userContext, $resources)
    {
        foreach ($resources as $resource) {
            $this->assertFalse(
                $this->_service->isAllowed($resource, $userContext),
                "Access to resource '{$resource}' is expected to be prohibited."
            );
        }
    }
}
