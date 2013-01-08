<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Adminhtml_DashboardControllerTest extends Mage_Backend_Utility_Controller
{
    public function testTunnelAction()
    {
        $testUrl = Mage_Adminhtml_Block_Dashboard_Graph::API_URL . '?cht=p3&chd=t:60,40&chs=250x100&chl=Hello|World';
        $handle = curl_init();
        curl_setopt($handle, CURLOPT_URL, $testUrl);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        try {
            if (false === curl_exec($handle)) {
                $this->markTestSkipped('Third-party service is unavailable: ' . $testUrl);
            }
            curl_close($handle);
        } catch (Exception $e) {
            curl_close($handle);
            throw $e;
        }

        $gaFixture = 'YTo5OntzOjM6ImNodCI7czoyOiJsYyI7czozOiJjaGYiO3M6Mzk6ImJnLHMsZjRmNGY0fGMsbGcsOTAsZmZmZmZ'
            . 'mLDAuMSxlZGVkZWQsMCI7czozOiJjaG0iO3M6MTQ6IkIsZjRkNGIyLDAsMCwwIjtzOjQ6ImNoY28iO3M6NjoiZGI0ODE0IjtzOjM6ImN'
            . 'oZCI7czoxNjoiZTpBQUFBQUFBQWYuQUFBQSI7czo0OiJjaHh0IjtzOjM6IngseSI7czo0OiJjaHhsIjtzOjc0OiIwOnwxMC8xMy8xMnw'
            . 'xMC8xNC8xMnwxMC8xNS8xMnwxMC8xNi8xMnwxMC8xNy8xMnwxMC8xOC8xMnwxMC8xOS8xMnwxOnwwfDF8MiI7czozOiJjaHMiO3M6Nzo'
            . 'iNTg3eDMwMCI7czozOiJjaGciO3M6MjI6IjE2LjY2NjY2NjY2NjY2Nyw1MCwxLDAiO30%3D';
        $helper = new Mage_Adminhtml_Helper_Dashboard_Data;
        $hash = $helper->getChartDataHash($gaFixture);
        $this->getRequest()->setParam('ga', $gaFixture)->setParam('h', $hash);
        $this->dispatch('backend/admin/dashboard/tunnel');
        $this->assertStringStartsWith("\x89\x50\x4E\x47", $this->getResponse()->getBody()); // PNG header
    }
}
