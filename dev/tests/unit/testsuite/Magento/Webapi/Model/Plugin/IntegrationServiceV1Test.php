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

    public function setUp()
    {
        $this->authzServiceMock = $this->getMockBuilder('\Magento\Authz\Service\AuthorizationV1')
            ->disableOriginalConstructor()
            ->setMethods(['removePermissions'])->getMock();
        $this->userIdentifierFactoryMock = $this->getMockBuilder('\Magento\Authz\Model\UserIdentifier\Factory')
            ->disableOriginalConstructor()
            ->setMethods(['create'])->getMock();
        $this->subjectMock = $this->getMock('Magento\Integration\Service\IntegrationV1', array(), array(), '', false);
        $this->integrationV1Plugin = new \Magento\Webapi\Model\Plugin\IntegrationServiceV1(
            $this->authzServiceMock,
            $this->userIdentifierFactoryMock
        );
    }

    public function testAfterDelete()
    {
        $integrationsData = array(
            Integration::ID => 1,
            Integration::NAME => 'TestIntegration1',
            Integration::EMAIL => 'test-integration1@magento.com',
            Integration::ENDPOINT => 'http://endpoint.com',
            Integration::SETUP_TYPE => 1,
        );
        $userIdentifierMock = $this->getMockBuilder('\Magento\Authz\Model\UserIdentifier')
            ->disableOriginalConstructor()
            ->getMock();
        $this->authzServiceMock->expects($this->once())
            ->method('removePermissions')->with($userIdentifierMock);
        $this->userIdentifierFactoryMock->expects($this->at(0))
            ->method('create')
            ->with(UserIdentifier::USER_TYPE_INTEGRATION, 1)
            ->will($this->returnValue($userIdentifierMock));
        $this->authzServiceMock->expects($this->once())
            ->method('removePermissions')
            ->with($userIdentifierMock);
        $this->integrationV1Plugin->afterDelete($this->subjectMock, $integrationsData);
    }
}