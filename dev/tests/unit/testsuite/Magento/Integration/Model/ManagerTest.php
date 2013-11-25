<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Integration\Model;

use Magento\Integration\Model\Integration;

/**
 * Class to test Integration Manager
 */
class ManagerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Integration service
     *
     * @var \Magento\Integration\Service\IntegrationV1Interface
     */
    protected $_integrationServiceMock;

    /**
     * Integration config
     *
     * @var \Magento\Integration\Model\Config
     */
    protected $_integrationConfigMock;

    /**
     * Integration config
     *
     * @var \Magento\Integration\Model\Manager
     */
    protected $_integrationManager;

    public function setUp()
    {
        $this->_integrationConfigMock = $this->getMockBuilder('\Magento\Integration\Model\Config')
            ->disableOriginalConstructor()
            ->setMethods(['getIntegrations'])->getMock();

        $this->_integrationServiceMock = $this->getMockBuilder('\Magento\Integration\Service\IntegrationV1')
            ->disableOriginalConstructor()
            ->setMethods(['create'])->getMock();

        $this->_integrationManager = new \Magento\Integration\Model\Manager(
            $this->_integrationConfigMock,
            $this->_integrationServiceMock
        );
    }

    public function tearDown()
    {
        unset($this->_integrationConfigMock);
        unset($this->_integrationServiceMock);
        unset($this->_integrationManager);
    }

    public function testProcessIntegrationConfigNoIntegrations()
    {
        $this->_integrationConfigMock->expects($this->never())
            ->method('getIntegrations');
        $this->_integrationManager->processIntegrationConfig(array());
    }

    public function testProcessIntegrationConfigSuccess()
    {
        $this->_integrationConfigMock->expects($this->once())
            ->method('getIntegrations')
            ->will(
                $this->returnValue(
                    array(
                        'TestIntegration1' => array(
                            'email' => 'test-integration1@magento.com',
                            'endpoint_url' => 'http://endpoint.com'
                        ),
                        'TestIntegration2' => array(
                            'email' => 'test-integration2@magento.com'
                        ),
                    )
                )
            );

        $integrationsData1 = array(
            Integration::NAME => 'TestIntegration1',
            Integration::EMAIL => 'test-integration1@magento.com',
            Integration::ENDPOINT => 'http://endpoint.com',
            Integration::SETUP_TYPE => 1,
        );

        $integrationsData2 = array(
            Integration::NAME => 'TestIntegration2',
            Integration::EMAIL => 'test-integration2@magento.com',
            Integration::SETUP_TYPE => 1,
        );

        $this->_integrationServiceMock->expects($this->at(0))
            ->method('create')
            ->with($integrationsData1);

        $this->_integrationServiceMock->expects($this->at(1))
            ->method('create')
            ->with($integrationsData2);

        $this->_integrationManager->processIntegrationConfig(array('TestIntegration1', 'TestIntegration2'));
    }
}