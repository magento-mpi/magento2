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
    protected $integrationConfigMock;

    /**
     * Integration service mock
     *
     * @var \Magento\Integration\Service\V1\IntegrationInterface
     */
    protected $integrationServiceMock;

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
     * @var \Magento\Webapi\Model\Plugin\Setup
     */
    protected $apiSetupPlugin;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $subjectMock;

    public function setUp()
    {
        $this->integrationConfigMock = $this->getMockBuilder(
            '\Magento\Webapi\Model\IntegrationConfig'
        )->disableOriginalConstructor()->setMethods(
            array('getIntegrations')
        )->getMock();

        $this->integrationServiceMock = $this->getMockBuilder(
            '\Magento\Integration\Service\V1\Integration'
        )->disableOriginalConstructor()->setMethods(
            array('findByName')
        )->getMock();

        $this->authzServiceMock = $this->getMockBuilder(
            '\Magento\Integration\Service\V1\AuthorizationService'
        )->disableOriginalConstructor()->setMethods(
            array('grantPermissions')
        )->getMock();

        $this->userIdentifierFactoryMock = $this->getMockBuilder(
            '\Magento\Authz\Model\UserIdentifier\Factory'
        )->disableOriginalConstructor()->setMethods(
            array('create')
        )->getMock();

        $this->subjectMock = $this->getMock('Magento\Integration\Model\Resource\Setup', array(), array(), '', false);
        $this->apiSetupPlugin = new \Magento\Webapi\Model\Plugin\Setup(
            $this->integrationConfigMock,
            $this->authzServiceMock,
            $this->integrationServiceMock,
            $this->userIdentifierFactoryMock
        );
    }

    public function testAfterInitIntegrationProcessingNoIntegrations()
    {
        $this->integrationConfigMock->expects($this->never())->method('getIntegrations');
        $this->integrationServiceMock->expects($this->never())->method('findByName');
        $this->authzServiceMock->expects($this->never())->method('grantPermissions');
        $this->userIdentifierFactoryMock->expects($this->never())->method('create');
        $this->apiSetupPlugin->afterInitIntegrationProcessing($this->subjectMock, array());
    }

    /**
     * @return void
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function testAfterInitIntegrationProcessingSuccess()
    {
        $testIntegration1Resource = array(
            'Magento_Customer::manage',
            'Magento_Customer::online',
            'Magento_Sales::create',
            'Magento_SalesRule::quote'
        );
        $testIntegration2Resource = array('Magento_Catalog::product_read');
        $this->integrationConfigMock->expects(
            $this->once()
        )->method(
            'getIntegrations'
        )->will(
            $this->returnValue(
                array(
                    'TestIntegration1' => array('resources' => $testIntegration1Resource),
                    'TestIntegration2' => array('resources' => $testIntegration2Resource)
                )
            )
        );
        $firstInegrationId = 1;

        $integrationsData1 = new \Magento\Framework\Object(
            array(
                'id' => $firstInegrationId,
                Integration::NAME => 'TestIntegration1',
                Integration::EMAIL => 'test-integration1@magento.com',
                Integration::ENDPOINT => 'http://endpoint.com',
                Integration::SETUP_TYPE => 1
            )
        );

        $secondIntegrationId = 2;
        $integrationsData2 = new \Magento\Framework\Object(
            array(
                'id' => $secondIntegrationId,
                Integration::NAME => 'TestIntegration2',
                Integration::EMAIL => 'test-integration2@magento.com',
                Integration::SETUP_TYPE => 1
            )
        );

        $this->integrationServiceMock->expects(
            $this->at(0)
        )->method(
            'findByName'
        )->with(
            'TestIntegration1'
        )->will(
            $this->returnValue($integrationsData1)
        );

        $this->integrationServiceMock->expects(
            $this->at(1)
        )->method(
            'findByName'
        )->with(
            'TestIntegration2'
        )->will(
            $this->returnValue($integrationsData2)
        );

        $this->authzServiceMock->expects(
            $this->at(0)
        )->method(
            'grantPermissions'
        )->with(
            $firstInegrationId,
            $testIntegration1Resource
        );
        $this->authzServiceMock->expects(
            $this->at(1)
        )->method(
            'grantPermissions'
        )->with(
            $secondIntegrationId,
            $testIntegration2Resource
        );

        $this->apiSetupPlugin->afterInitIntegrationProcessing(
            $this->subjectMock,
            array('TestIntegration1', 'TestIntegration2')
        );
    }
}
