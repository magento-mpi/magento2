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

class SetupTest extends \PHPUnit_Framework_TestCase
{
    /**
     * API Integration config
     *
     * @var \Magento\Webapi\Model\IntegrationConfig
     */
    protected $_integrationConfigMock;

    /**
     * Integration service mock
     *
     * @var \Magento\Integration\Service\IntegrationV1Interface
     */
    protected $_integrationServiceMock;

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
     * @var \Magento\Webapi\Model\Plugin\Setup
     */
    protected $_apiSetupPlugin;

    public function setUp()
    {
        $this->_integrationConfigMock = $this->getMockBuilder('\Magento\Webapi\Model\IntegrationConfig')
            ->disableOriginalConstructor()
            ->setMethods(['getIntegrations'])->getMock();

        $this->_integrationServiceMock = $this->getMockBuilder('\Magento\Integration\Service\IntegrationV1')
            ->disableOriginalConstructor()
            ->setMethods(['findByName'])->getMock();

        $this->_authzServiceMock = $this->getMockBuilder('\Magento\Authz\Service\AuthorizationV1')
            ->disableOriginalConstructor()
            ->setMethods(['grantPermissions'])->getMock();

        $this->_userIdentifierFactoryMock = $this->getMockBuilder('\Magento\Authz\Model\UserIdentifier\Factory')
            ->disableOriginalConstructor()
            ->setMethods(['create'])->getMock();

        $this->_apiSetupPlugin = new \Magento\Webapi\Model\Plugin\Setup(
            $this->_integrationConfigMock,
            $this->_authzServiceMock,
            $this->_integrationServiceMock,
            $this->_userIdentifierFactoryMock
        );
    }

    public function testAfterInitIntegrationProcessingNoIntegrations()
    {
        $this->_integrationConfigMock->expects($this->never())
            ->method('getIntegrations');
        $this->_integrationServiceMock->expects($this->never())
            ->method('findByName');
        $this->_authzServiceMock->expects($this->never())
            ->method('grantPermissions');
        $this->_userIdentifierFactoryMock->expects($this->never())
            ->method('create');
        $this->_apiSetupPlugin->afterInitIntegrationProcessing(array());
    }

    public function testAfterInitIntegrationProcessingSuccess()
    {
        $testIntegration1Resource = array(
            'Magento_Customer::manage',
            'Magento_Customer::online',
            'Magento_Sales::create',
            'Magento_SalesRule::quote'
        );
        $testIntegration2Resource = array(
            'Magento_Catalog::product_read'
        );
        $this->_integrationConfigMock->expects($this->once())
            ->method('getIntegrations')
            ->will(
                $this->returnValue(
                    array(
                        'TestIntegration1' => array(
                            'resources' => $testIntegration1Resource
                        ),
                        'TestIntegration2' => array(
                            'resources' => $testIntegration2Resource
                        ),
                    )
                )
            );

        $integrationsData1 = new \Magento\Object(array(
            'id' => 1,
            Integration::NAME => 'TestIntegration1',
            Integration::EMAIL => 'test-integration1@magento.com',
            Integration::ENDPOINT => 'http://endpoint.com',
            Integration::SETUP_TYPE => 1,
        ));

        $integrationsData2 = new \Magento\Object(array(
            'id' => 2,
            Integration::NAME => 'TestIntegration2',
            Integration::EMAIL => 'test-integration2@magento.com',
            Integration::SETUP_TYPE => 1,
        ));

        $this->_integrationServiceMock->expects($this->at(0))
            ->method('findByName')
            ->with('TestIntegration1')
            ->will($this->returnValue($integrationsData1));

        $this->_integrationServiceMock->expects($this->at(1))
            ->method('findByName')
            ->with('TestIntegration2')
            ->will($this->returnValue($integrationsData2));

        $userIdentifierMock1 = $this->getMockBuilder('\Magento\Authz\Model\UserIdentifier')
            ->disableOriginalConstructor()
            ->getMock();
        $this->_userIdentifierFactoryMock->expects($this->at(0))
            ->method('create')
            ->with(UserIdentifier::USER_TYPE_INTEGRATION, 1)
            ->will($this->returnValue($userIdentifierMock1));

        $userIdentifierMock2 = $this->getMockBuilder('\Magento\Authz\Model\UserIdentifier')
            ->disableOriginalConstructor()
            ->getMock();
        $this->_userIdentifierFactoryMock->expects($this->at(1))
            ->method('create')
            ->with(UserIdentifier::USER_TYPE_INTEGRATION, 2)
            ->will($this->returnValue($userIdentifierMock2));

        $this->_authzServiceMock->expects($this->at(0))
            ->method('grantPermissions')
            ->with($userIdentifierMock1, $testIntegration1Resource);
        $this->_authzServiceMock->expects($this->at(1))
            ->method('grantPermissions')
            ->with($userIdentifierMock2, $testIntegration2Resource);

        $this->_apiSetupPlugin->afterInitIntegrationProcessing(array('TestIntegration1', 'TestIntegration2'));

    }
}
