<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Saas_Saas_Model_Http_HandlerTest extends PHPUnit_Framework_TestCase
{
    public function testHandle()
    {
        $config = $this->getMockBuilder('Mage_Core_Model_Config_Primary')->disableOriginalConstructor()->getMock();
        $config->expects($this->any())->method('getParam')
            ->with($this->equalTo('status'))
            ->will($this->returnValue(Saas_Saas_Model_Tenant_Config::STATUS_DISABLED_FRONTEND));

        $config->expects($this->any())->method('getNode')
            ->with($this->equalTo('global/areas/adminhtml/frontName'))
            ->will($this->returnValue('fakebackend'));

        $maintenanceConfig = $this->getMockBuilder('Saas_Saas_Model_Maintenance_Config')
            ->disableOriginalConstructor()->getMock();
        $maintenanceConfig->expects($this->any())->method('getUrl')->will($this->returnValue('gostorego.com'));

        $request = $this->getMockBuilder('Zend_Controller_Request_Http')->disableOriginalConstructor()->getMock();
        $request->expects($this->once())->method('getPathInfo')->will($this->returnValue('/fakefrontendend/test'));
        $request->expects($this->once())->method('setDispatched')->with($this->equalTo(true));

        $response = $this->getMockBuilder('Zend_Controller_Response_Http')->disableOriginalConstructor()->getMock();
        $response->expects($this->once())->method('setRedirect')->with($this->equalTo('gostorego.com'));
        $response->expects($this->once())->method('sendResponse');

        $handler = new Saas_Saas_Model_Http_Handler($config, $maintenanceConfig);
        $handler->handle($request, $response);
    }

    /**
     * @dataProvider handleNegativeDataProvider
     * @param $status
     * @param $path
     */
    public function testHandleNegative($status, $path)
    {
        $config = $this->getMockBuilder('Mage_Core_Model_Config_Primary')->disableOriginalConstructor()->getMock();
        $config->expects($this->any())->method('getParam')
            ->with($this->equalTo('status'))
            ->will($this->returnValue($status));

        $config->expects($this->any())->method('getNode')
            ->with($this->equalTo('global/areas/adminhtml/frontName'))
            ->will($this->returnValue('fakebackend'));

        $maintenanceConfig = $this->getMockBuilder('Saas_Saas_Model_Maintenance_Config')
            ->disableOriginalConstructor()->getMock();
        $maintenanceConfig->expects($this->any())->method('getUrl')->will($this->returnValue('gostorego.com'));

        $request = $this->getMockBuilder('Zend_Controller_Request_Http')->disableOriginalConstructor()->getMock();
        $request->expects($this->any())->method('getPathInfo')->will($this->returnValue($path));
        $request->expects($this->never())->method('setDispatched');

        $response = $this->getMockBuilder('Zend_Controller_Response_Http')->disableOriginalConstructor()->getMock();
        $response->expects($this->never())->method('setRedirect');
        $response->expects($this->never())->method('sendResponse');

        $handler = new Saas_Saas_Model_Http_Handler($config, $maintenanceConfig);
        $handler->handle($request, $response);
    }

    public function handleNegativeDataProvider()
    {
        return array (
            'Not Disabled Status' => array(Saas_Saas_Model_Tenant_Config::STATUS_ENABLED, '/fakefrontendend/test'),
            'Backend with Disabled Status' => array(
                Saas_Saas_Model_Tenant_Config::STATUS_DISABLED_FRONTEND,
                '/fakebackend/test',
            )
        );
    }
}
