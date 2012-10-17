<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Adminhtml_DashboardControllerTest extends Mage_Adminhtml_Utility_Controller
{
    public function testTunnelAction()
    {
        $this->dispatch('backend/admin/dashboard/tunnel');
        $this->assertEquals('Service unavailable: invalid request', $this->getResponse()->getBody());
        $this->assertEquals(503, $this->getResponse()->getHttpResponseCode());
    }
}
