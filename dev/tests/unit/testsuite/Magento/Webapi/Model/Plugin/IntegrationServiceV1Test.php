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
    protected $_authzServiceMock;

    /**
     * Mock for UserIdentifier Factory
     *
     * @var \Magento\Authz\Model\UserIdentifier\Factory
     */
    protected $_userIdentifierFactoryMock;

    /**
     * API setup plugin
     *
     * @var \Magento\Webapi\Model\Plugin\IntegrationServiceV1
     */
    protected $_integrationV1Plugin;

    public function setUp()
    {
        $this->_authzServiceMock = $this->getMockBuilder('\Magento\Authz\Service\AuthorizationV1')
            ->disableOriginalConstructor()
            ->setMethods(['removePermissions'])->getMock();
        $this->_userIdentifierFactoryMock = $this->getMockBuilder('\Magento\Authz\Model\UserIdentifier\Factory')
            ->disableOriginalConstructor()
            ->setMethods(['create'])->getMock();
        $this->_integrationV1Plugin = new \Magento\Webapi\Model\Plugin\IntegrationServiceV1(
            $this->_authzServiceMock,
            $this->_userIdentifierFactoryMock
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
        $this->_authzServiceMock->expects($this->once())
            ->method('removePermissions')->with($userIdentifierMock);
        $this->_userIdentifierFactoryMock->expects($this->at(0))
            ->method('create')
            ->with(UserIdentifier::USER_TYPE_INTEGRATION, 1)
            ->will($this->returnValue($userIdentifierMock));
        $this->_authzServiceMock->expects($this->once())
            ->method('removePermissions')
            ->with($userIdentifierMock);
        $this->_integrationV1Plugin->afterDelete($integrationsData);
    }
}