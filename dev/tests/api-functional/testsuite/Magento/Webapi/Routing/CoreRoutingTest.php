<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Class to test Core Web API routing
 */
namespace Magento\Webapi\Routing;

class CoreRoutingTest extends \Magento\Webapi\Routing\BaseService
{
    public function testBasicRoutingPathAutoDetection()
    {
        $itemId = 1;
        $serviceInfo = array(
            'serviceInterface' => 'Magento\TestModule1\Service\V1\AllSoapAndRestInterface',
            'method' => 'item',
            'entityId' => $itemId
        );
        $requestData = array('id' => $itemId);
        $item = $this->_webApiCall($serviceInfo, $requestData);
        $this->assertEquals($itemId, $item['id'], "Item was retrieved unsuccessfully");
    }

    public function testBasicRoutingExplicitPath()
    {
        $itemId = 1;
        $serviceInfo = array(
            'rest' => array(
                'resourcePath' => '/V1/testmodule1/' . $itemId,
                'httpMethod' => \Magento\Webapi\Model\Rest\Config::HTTP_METHOD_GET
            ),
            'soap' => array(
                'service' => 'testModule1AllSoapAndRestV1',
                'operation' => 'testModule1AllSoapAndRestV1Item'
            )
        );
        $requestData = array('id' => $itemId);
        $item = $this->_webApiCall($serviceInfo, $requestData);
        $this->assertEquals($itemId, $item['id'], "Item was retrieved unsuccessfully");
    }

    public function testDisabledIntegrationAuthorizationException()
    {
        $itemId = 1;
        $serviceInfo = array(
            'rest' => array(
                'resourcePath' => '/V1/testmodule1/' . $itemId,
                'httpMethod' => \Magento\Webapi\Model\Rest\Config::HTTP_METHOD_GET
            ),
            'soap' => array(
                'service' => 'testModule1AllSoapAndRestV1',
                'operation' => 'testModule1AllSoapAndRestV1Item'
            )
        );
        $requestData = array('id' => $itemId);

        /** Disable integration associated with active OAuth credentials. */
        $credentials = \Magento\TestFramework\Authentication\OauthHelper::getApiAccessCredentials();
        /** @var \Magento\Integration\Model\Integration $integration */
        $integration = $credentials['integration'];
        $originalStatus = $integration->getStatus();
        $integration->setStatus(\Magento\Integration\Model\Integration::STATUS_INACTIVE)->save();

        try {
            $this->assertUnauthorizedException($serviceInfo, $requestData);
        } catch (\Exception $e) {
            /** Restore original status of integration associated with active OAuth credentials */
            $integration->setStatus($originalStatus)->save();
            throw $e;
        }
        $integration->setStatus($originalStatus)->save();
    }

    public function testExceptionSoapInternalError()
    {
        $this->_markTestAsSoapOnly();
        $serviceInfo = array(
            'serviceInterface' => 'Magento\TestModule3\Service\V1\ErrorInterface',
            'method' => 'serviceException',
        );
        $this->setExpectedException(
            'SoapFault',
            'Generic service exception'
        );
        $this->_webApiCall($serviceInfo);
    }
}
