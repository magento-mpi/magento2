<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Adminhtml\Controller;

/**
 * @magentoAppArea adminhtml
 */
class DashboardTest extends \Magento\Backend\Utility\Controller
{
    public function testAjaxBlockAction()
    {
        $this->getRequest()->setParam('block', 'tab_orders');
        $this->dispatch('backend/admin/dashboard/ajaxBlock');

        $actual = $this->getResponse()->getBody();
        $this->assertContains('dashboard-diagram', $actual);
    }
}
