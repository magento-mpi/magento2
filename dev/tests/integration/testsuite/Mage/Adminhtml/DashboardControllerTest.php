<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Adminhtml_DashboardControllerTest extends Mage_Adminhtml_Utility_Controller
{
    public function testTunnelActionInvalid()
    {
        $this->dispatch('backend/admin/dashboard/tunnel');
        $this->assertEquals(400, $this->getResponse()->getHttpResponseCode());
    }
}
