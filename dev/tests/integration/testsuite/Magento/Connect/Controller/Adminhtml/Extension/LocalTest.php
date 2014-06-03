<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Connect\Controller\Adminhtml\Extension;

/**
 * Test \Magento\Connect\Controller\Adminhtml\Extension\Local
 *
 * @magentoAppArea adminhtml
 */
class LocalTest extends \Magento\Backend\Utility\Controller
{
    public function testIndexAction()
    {
        $this->dispatch('backend/admin/extension_local/index');
        $expected = '?return=' . urlencode(
            \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
                'Magento\Backend\Helper\Data'
            )->getHomePageUrl()
        );
        $this->assertRedirect($this->stringEndsWith($expected));
    }
}
