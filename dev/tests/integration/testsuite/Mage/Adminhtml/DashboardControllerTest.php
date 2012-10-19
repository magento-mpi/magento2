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

    public function testTunnelActionError()
    {
        $gaFixture = 'YTo5OntzOjM6ImNodCI7czoyOiJsYyI7czozOiJjaGYiO3M6Mzk6ImJnLHMsZjRmNGY0fGMsbGcsOTAsZmZmZmZ'
            . 'mLDAuMSxlZGVkZWQsMCI7czozOiJjaG0iO3M6MTQ6IkIsZjRkNGIyLDAsMCwwIjtzOjQ6ImNoY28iO3M6NjoiZGI0ODE0IjtzOjM6ImN'
            . 'oZCI7czoxNjoiZTpBQUFBQUFBQWYuQUFBQSI7czo0OiJjaHh0IjtzOjM6IngseSI7czo0OiJjaHhsIjtzOjc0OiIwOnwxMC8xMy8xMnw'
            . 'xMC8xNC8xMnwxMC8xNS8xMnwxMC8xNi8xMnwxMC8xNy8xMnwxMC8xOC8xMnwxMC8xOS8xMnwxOnwwfDF8MiI7czozOiJjaHMiO3M6Nzo'
            . 'iNTg3eDMwMCI7czozOiJjaGciO3M6MjI6IjE2LjY2NjY2NjY2NjY2Nyw1MCwxLDAiO30%3D';
        $helper = new Mage_Adminhtml_Helper_Dashboard_Data;
        $hash = $helper->getChartDataHash($gaFixture);
        $this->getRequest()->setParam('ga', $gaFixture)->setParam('h', $hash);
        $this->_response = $this->getMock('Magento_Test_Response', array('setBody'));
        $this->_runOptions['response'] = $this->_response;
        $this->_response->expects($this->at(0))->method('setBody')->will($this->throwException(new Exception));
        $this->_response->expects($this->at(1))->method('setBody')->will($this->returnSelf());
        $this->dispatch('backend/admin/dashboard/tunnel');
        $this->assertEquals(503, $this->getResponse()->getHttpResponseCode());
    }
}
