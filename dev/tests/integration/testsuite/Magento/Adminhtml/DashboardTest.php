<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @magentoAppArea adminhtml
 */
class Magento_Adminhtml_DashboardTest extends Magento_Backend_Utility_Controller
{
    public function testTunnelAction()
    {
        $testUrl = \Magento\Adminhtml\Block\Dashboard\Graph::API_URL .
            '?cht=p3&chd=t:60,40&chs=250x100&chl=Hello|World';
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

        $gaData = array(
            'cht' => 'lc',
            'chf' => 'bg,s,f4f4f4|c,lg,90,ffffff,0.1,ededed,0',
            'chm' => 'B,f4d4b2,0,0,0',
            'chco' => 'db4814',
            'chd' => 'e:AAAAAAAAf.AAAA',
            'chxt' => 'x,y',
            'chxl' => '0:|10/13/12|10/14/12|10/15/12|10/16/12|10/17/12|10/18/12|10/19/12|1:|0|1|2',
            'chs' => '587x300',
            'chg' => '16.666666666667,50,1,0',
        );
        $gaFixture = urlencode(base64_encode(json_encode($gaData)));

        /** @var $helper Magento_Adminhtml_Helper_Dashboard_Data */
        $helper = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->get('Magento\Adminhtml\Helper\Dashboard\Data');
        $hash = $helper->getChartDataHash($gaFixture);
        $this->getRequest()->setParam('ga', $gaFixture)->setParam('h', $hash);
        $this->dispatch('backend/admin/dashboard/tunnel');
        $this->assertStringStartsWith("\x89\x50\x4E\x47", $this->getResponse()->getBody()); // PNG header
    }
}
