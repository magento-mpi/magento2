<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webapi\Model\Plugin;

use Magento\Authz\Model\UserIdentifier;
use Magento\Integration\Model\Integration;
use Magento\Authorization\Model\Acl\AclRetriever;

class IntegrationServiceV1Test extends \PHPUnit_Framework_TestCase
{
    /**
     * Authorization service mock
     *
     * @var \Magento\Authz\Service\AuthorizationV1
     */
    protected $authzServiceMock;

    /**
     * Mock for UserIdentifier Factory
     *
     * @var \Magento\Authz\Model\UserIdentifier\Factory
     */
    protected $userIdentifierFactoryMock;

    /**
     * API setup plugin
     *
     * @var \Magento\Webapi\Model\Plugin\IntegrationServiceV1
     */
    protected $integrationV1Plugin;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $subjectMock;

    /** @var  AclRetriever */
    protected $aclRetrieverMock;

    /**
     * @var \Magento\Integration\Service\V1\AuthorizationServiceInterface
     */
    protected $integrationAuthServiceMock;

    public function setUp()
    {
        $this->authzServiceMock = $this->getMockBuilder(
            '\Magento\Authz\Service\AuthorizationV1'
        )->disableOriginalConstructor()->setMethods(
            array('removePermissions')
        )->getMock();
        $this->userIdentifierFactoryMock = $this->getMockBuilder(
            '\Magento\Authz\Model\UserIdentifier\Factory'
        )->disableOriginalConstructor()->setMethods(
            array('create')
        )->getMock();
        $this->subjectMock = $this->getMock('Magento\Integration\Service\V1\Integration', array(), array(), '', false);
        $this->integrationAuthServiceMock = $this->getMockBuilder(
            'Magento\Integration\Service\V1\AuthorizationServiceInterface'
        )->disableOriginalConstructor()->getMock();
        $this->aclRetrieverMock = $this->getMockBuilder('Magento\Authorization\Model\Acl\AclRetriever')
            ->disableOriginalConstructor()
            ->getMock();
        $this->integrationV1Plugin = new \Magento\Webapi\Model\Plugin\IntegrationServiceV1(
            $this->authzServiceMock,
            $this->userIdentifierFactoryMock,
            $this->integrationAuthServiceMock,
            $this->aclRetrieverMock
        );
    }

    public function testAfterDelete()
    {
        $integrationId = 1;
        $integrationsData = array(
            Integration::ID => $integrationId,
            Integration::NAME => 'TestIntegration1',
            Integration::EMAIL => 'test-integration1@magento.com',
            Integration::ENDPOINT => 'http://endpoint.com',
            Integration::SETUP_TYPE => 1
        );

        $this->integrationAuthServiceMock->expects($this->once())
            ->method('removePermissions')
            ->with($integrationId);
        $this->integrationV1Plugin->afterDelete($this->subjectMock, $integrationsData);
    }
}
